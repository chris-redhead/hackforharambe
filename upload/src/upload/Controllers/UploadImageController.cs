using System;
using System.Collections.Generic;
using System.Linq;
using System.Threading.Tasks;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Http;

// For more information on enabling MVC for empty projects, visit http://go.microsoft.com/fwlink/?LinkID=397860

namespace upload.Controllers
{
    public class UploadImageController : Controller
    {
        private static int hiddenChunkLength = 20;

        [HttpGet]
        // GET: /<controller>/
        public IActionResult Index()
        {
            return View();
        }

        [HttpPost]
        public async Task<IActionResult> Upload(ICollection<IFormFile> evilfile)
        {
            foreach(var file in evilfile)
            {
                byte[] imageBytes;
                List<byte[]> chunkList = new List<byte[]>();

                using (var fileStream = file.OpenReadStream())
                {
                    var length = fileStream.Length;

                    int offset = 0;

                    while(offset < fileStream.Length)
                    {
                        imageBytes = new byte[hiddenChunkLength];
                        //read into array
                        await fileStream.ReadAsync(imageBytes, 0, hiddenChunkLength);

                        chunkList.Add(imageBytes);
                        offset = offset + hiddenChunkLength;
                    }            
                }

                foreach (var chunk in chunkList)
                {
                    hitEncodingEndpoint(chunk);
                }
            }

            ViewBag.message = "Successfully uploaded";
            return View("Index");
        }

        private void hitEncodingEndpoint(byte[] chunk)
        {
            //hit the endpoint here

        }
    }
}
