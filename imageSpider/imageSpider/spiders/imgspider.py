from imageSpider.items import ImageItem
from scrapy.selector import Selector
import scrapy

class ImageSpider(scrapy.Spider):
    name = 'pyImageSearch'
    start_urls =['http://imgur.com/search/score/all?q_any=cat&q_type=png&q_size_px=med&q_size_mpx=med&q_all=']

    def parse(self, response):
        images = Selector(response).xpath('//div[@class="post"]/a')

        for image in images:
            item = ImageItem()
            imageURL = image.xpath('img/@src').extract_first()
            imageURL = ('http://' + imageURL[2:])[:-4] + '.png'
            item['file_urls'] = [imageURL]
            yield item

