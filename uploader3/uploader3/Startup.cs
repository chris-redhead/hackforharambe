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
using System.Threading.Tasks;
using System.Web;
using System.Web.Http;
using uploader3;

[assembly: OwinStartup(typeof(Startup))]

namespace uploader3
{
    public class UploadController : ApiController
    {
        public UploadController() {

        }

        [HttpGet]
        [Route("hello")]
        public Task<string> GetHello() => Task.FromResult("HELLO!");





        Stream ConvertCarrierImage(Stream inp) {
            var img = Image.FromStream(inp);

            var dest = new Bitmap(2048, 1024);
            dest.SetResolution(img.HorizontalResolution, img.VerticalResolution);
            
            using(var g = Graphics.FromImage(dest)) {
                g.CompositingMode = CompositingMode.SourceCopy;
                g.CompositingQuality = CompositingQuality.HighQuality;
                g.InterpolationMode = InterpolationMode.HighQualityBicubic;
                g.SmoothingMode = SmoothingMode.HighQuality;
                g.PixelOffsetMode = PixelOffsetMode.HighQuality;

                g.DrawImage(img, new[] { new Point(0, 0), new Point(dest.Width, 0), new Point(0, dest.Height) });
            }

            var str = new MemoryStream();
            dest.Save(str, ImageFormat.Jpeg);

            return str;
        }



        Stream ConvertEvilImage(Stream inp) {
            var img = Image.FromStream(inp);

            var dest = new Bitmap(100, 100);
            dest.SetResolution(img.HorizontalResolution, img.VerticalResolution);
            
            using(var g = Graphics.FromImage(dest)) {
                g.CompositingMode = CompositingMode.SourceCopy;
                g.CompositingQuality = CompositingQuality.HighQuality;
                g.InterpolationMode = InterpolationMode.HighQualityBicubic;
                g.SmoothingMode = SmoothingMode.HighQuality;
                g.PixelOffsetMode = PixelOffsetMode.HighQuality;

                g.DrawImage(img, new[] { new Point(0, 0), new Point(dest.Width, 0), new Point(0, dest.Height) });
            }

            try {
                var filter = new Threshold(100);
                filter.ApplyInPlace(dest);
            }
            catch(Exception e) {
                //!!!!!!!!!!!!!!!!!!!!!!!!!!!
            }
            
            var str = new MemoryStream();
            dest.Save(str, ImageFormat.Png);

            return str;
        }




        IEnumerable<Stream> GetExtraCarriers() 
        {
            var rand = new Random();

            while(true) {
                using(var http = new HttpClient()) 
                {                    
                    var r = ((rand.NextDouble() / 3) + 0.5);
                    var w = r * 2048;
                    var h = r * 1280;

                    var res = http.GetAsync($"http://placekitten.com/{(int)w}/{(int)h}").Result;

                    yield return res.Content.ReadAsStreamAsync().Result;
                }
            }
        }



        [HttpPost]
        [Route("upload")]
        public async Task<string[]> PostMessageImage() 
        {
            var data = await Request.Content.ReadAsMultipartAsync();

            var evilCont = data.Contents.Single(c => c.Headers.ContentDisposition.Name == "\"evil\"");
            var evil = ConvertEvilImage(await evilCont.ReadAsStreamAsync());
            
            var carrierConts = data.Contents.Where(c => c.Headers.ContentDisposition.Name == "\"carrier\"");
            var carrierStreams = await Task.WhenAll(carrierConts.Select(c => c.ReadAsStreamAsync()));
            
            carrierStreams = carrierStreams.Concat(GetExtraCarriers()).Take(13).ToArray();
                        
            var carriers = carrierStreams.Select(s => ConvertCarrierImage(s));


            using(var http = new HttpClient()) {
                
                var msg = new HttpRequestMessage(HttpMethod.Post, "http://192.168.43.196/jase/encrypt.php?submit=hello");

                var formData = new MultipartFormDataContent();

                formData.Add(new StreamContent(evil));

                foreach(var carrier in carriers) {
                    formData.Add(new StreamContent(carrier));
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