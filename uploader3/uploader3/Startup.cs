using AForge.Imaging.Filters;
using Microsoft.Owin;
using Microsoft.Owin.Cors;
using Owin;
using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Drawing.Imaging;
using System.IO;
using System.Linq;
using System.Net.Http;
using System.Runtime.InteropServices;
using System.Threading.Tasks;
using System.Web;
using System.Web.Http;
using uploader3;

[assembly: OwinStartup(typeof(Startup))]

namespace uploader3
{

    public class Resizer
    {
        
        public Image ConvertCarrierImage(Image img) {
            var dest = new Bitmap(2048, 1280);
            dest.SetResolution(img.HorizontalResolution, img.VerticalResolution);

            using(var g = Graphics.FromImage(dest)) {
                g.CompositingMode = CompositingMode.SourceCopy;
                g.CompositingQuality = CompositingQuality.HighQuality;
                g.InterpolationMode = InterpolationMode.HighQualityBicubic;
                g.SmoothingMode = SmoothingMode.HighQuality;
                g.PixelOffsetMode = PixelOffsetMode.HighQuality;

                g.DrawImage(img, new Rectangle(0, 0, dest.Width, dest.Height));
            }

            return dest;
        }


        public static Bitmap ConvertTo1Bit(Bitmap input) {
            var masks = new byte[] { 0x80, 0x40, 0x20, 0x10, 0x08, 0x04, 0x02, 0x01 };
            var output = new Bitmap(input.Width, input.Height, PixelFormat.Format1bppIndexed);
            var data = new sbyte[input.Width, input.Height];
            var inputData = input.LockBits(new Rectangle(0, 0, input.Width, input.Height), ImageLockMode.ReadOnly, PixelFormat.Format24bppRgb);
            try {
                var scanLine = inputData.Scan0;
                var line = new byte[inputData.Stride];
                for(var y = 0; y < inputData.Height; y++, scanLine += inputData.Stride) {
                    Marshal.Copy(scanLine, line, 0, line.Length);
                    for(var x = 0; x < input.Width; x++) {
                        data[x, y] = (sbyte)(64 * (GetGreyLevel(line[x * 3 + 2], line[x * 3 + 1], line[x * 3 + 0]) - 0.5));
                    }
                }
            }
            finally {
                input.UnlockBits(inputData);
            }
            var outputData = output.LockBits(new Rectangle(0, 0, output.Width, output.Height), ImageLockMode.WriteOnly, PixelFormat.Format1bppIndexed);
            try {
                var scanLine = outputData.Scan0;
                for(var y = 0; y < outputData.Height; y++, scanLine += outputData.Stride) {
                    var line = new byte[outputData.Stride];
                    for(var x = 0; x < input.Width; x++) {
                        var j = data[x, y] > 0;
                        if(j) line[x / 8] |= masks[x % 8];
                        var error = (sbyte)(data[x, y] - (j ? 32 : -32));
                        if(x < input.Width - 1) data[x + 1, y] += (sbyte)(7 * error / 16);
                        if(y < input.Height - 1) {
                            if(x > 0) data[x - 1, y + 1] += (sbyte)(3 * error / 16);
                            data[x, y + 1] += (sbyte)(5 * error / 16);
                            if(x < input.Width - 1) data[x + 1, y + 1] += (sbyte)(1 * error / 16);
                        }
                    }
                    Marshal.Copy(line, 0, scanLine, outputData.Stride);
                }
            }
            finally {
                output.UnlockBits(outputData);
            }
            return output;
        }

        public static double GetGreyLevel(byte r, byte g, byte b) {
            return (r * 0.299 + g * 0.587 + b * 0.114) / 255;
        }


        public Image ConvertEvilImage(Image img) 
        {
            var intermediate = new Bitmap(100, 100, PixelFormat.Format24bppRgb);
            intermediate.SetResolution(img.HorizontalResolution, img.VerticalResolution);

            using(var g = Graphics.FromImage(intermediate)) {
                g.CompositingMode = CompositingMode.SourceCopy;
                g.CompositingQuality = CompositingQuality.HighQuality;
                g.InterpolationMode = InterpolationMode.HighQualityBicubic;
                g.SmoothingMode = SmoothingMode.HighQuality;
                g.PixelOffsetMode = PixelOffsetMode.HighQuality;
                
                g.DrawImage(img, new Rectangle(0, 0, intermediate.Width, intermediate.Height), 0, 0, img.Width, img.Height, GraphicsUnit.Pixel);               
            }

            return ConvertTo1Bit(intermediate);
        }



    }



    public class UploadController : ApiController
    {
        Resizer _resizer = new Resizer();

        public UploadController() {
            //...
        }

        [HttpGet]
        [Route("hello")]
        public Task<string> GetHello() => Task.FromResult("HELLO!");






        IEnumerable<CarrierData> GetExtraCarriers() 
        {
            var rand = new Random();

            while(true) {
                using(var http = new HttpClient()) {
                    var r = ((rand.NextDouble() / 3) + 0.5);
                    var w = r * 2048;
                    var h = r * 1280;

                    var res = http.GetAsync($"http://placekitten.com/{(int)w}/{(int)h}").Result;

                    yield return new CarrierData() {
                        Content = res.Content,
                        Image = Image.FromStream(res.Content.ReadAsStreamAsync().Result)
                    };
                }
            }
        }


        class CarrierData
        {
            public HttpContent Content;
            public Image Image;
        }



        [HttpPost]
        [Route("upload")]
        public async Task<string[]> PostMessageImage() {
            var data = await Request.Content.ReadAsMultipartAsync();

            var evilCont = data.Contents.Single(c => c.Headers.ContentDisposition.Name == "\"evil\"");
            var evil = Image.FromStream(await evilCont.ReadAsStreamAsync());
            evil = _resizer.ConvertEvilImage(evil);

            var carrierConts = data.Contents.Where(c => c.Headers.ContentDisposition.Name == "\"carrier\"");
            var carrierDatas = await Task.WhenAll(carrierConts
                                                        .Select(async c => new CarrierData {
                                                            Image = Image.FromStream(await c.ReadAsStreamAsync()),
                                                            Content = c
                                                        }));

            carrierDatas = carrierDatas.Concat(GetExtraCarriers()).Take(13).ToArray();

            var carriers = carrierDatas.Select(d => new CarrierData {
                Image = _resizer.ConvertCarrierImage(d.Image),
                Content = d.Content
            });


            using(var http = new HttpClient()) {
                
                var msg = new HttpRequestMessage(HttpMethod.Post, "http://192.168.43.196/jase/encrypt.php?submit=hello");

                var formData = new MultipartFormDataContent();

                formData.Headers.ContentEncoding.Add("multipart-form-data");

                {
                    var str = new MemoryStream();
                    evil.Save(str, ImageFormat.Png);
                    var bytes = str.ToArray();

                    str.Flush();

                    formData.Add(new ByteArrayContent(bytes)  /*new StreamContent(str)*/, "userfile[]", "evil.png"/*, evilCont.Headers.ContentDisposition.FileName*/);
                    str.Flush();
                }

                {
                    foreach(var carrier in carriers) {
                        var str = new MemoryStream();
                        carrier.Image.Save(str, ImageFormat.Jpeg);
                        var bytes = str.ToArray();
                        str.Flush();

                        formData.Add(new ByteArrayContent(bytes), "userfile[]", Guid.NewGuid() + ".jpg"); //, carrier.Content.Headers.ContentDisposition.FileName); //filename is needed here too?
                        str.Flush();
                    }
                }

                msg.Content = formData;

                var resp = await http.SendAsync(msg);

                var body = await resp.Content.ReadAsStreamAsync();

                using(var reader = new StreamReader(body)) 
                {
                    var encodedUrls = new List<string>();

                    while(true) {
                        var line = await reader.ReadLineAsync();
                        if(string.IsNullOrEmpty(line)) break;

                        encodedUrls.Add(line);
                    }

                    return encodedUrls.ToArray();
                }
            }
        }

    }
    


    public class Startup
    {
        public void Configuration(IAppBuilder app) 
        {            
            app.UseCors(CorsOptions.AllowAll);
            
            var conf = new HttpConfiguration();
            conf.MapHttpAttributeRoutes();
            conf.EnsureInitialized();
            app.UseWebApi(conf);
        }
    }

}