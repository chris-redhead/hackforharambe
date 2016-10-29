from PIL import Image, ImageMath, ImageColor
import sys

colorNames = ['red', 'green', 'blue']

def encrypt(original, watermark, output):
    switcher = False
    if original.endswith('.jpg'):
        switcher = True

    watermark = Image.open(watermark)
    original = Image.open(original)
    watermark = watermark.resize(original.size)

    if switcher:
        red, green, blue = original.split()
        wred, wgreen, wblue = watermark.split()
    else:
        red, green, blue, alpha = original.split()
        wred, wgreen, wblue, walpha = watermark.split()

    red2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = red, b = wred)
    green2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = green, b = wgreen)
    blue2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = blue, b = wblue)

    out = Image.merge("RGB", (red2, green2, blue2))
    out.save(output)

def decrypt(water, output):
    outputs = []
    for colName, color in zip(colorNames, water.split()):
        #print color
        watermark = ImageMath.eval("(a&0x1)*255", a = color)
        watermark = watermark.convert("L")
        outputs.append(watermark)
        #watermark.save(colName + output)
    
    out = Image.merge("RGB", (outputs[0], outputs[1], outputs[2]))
    out.save(output)

#main
argLength = len(sys.argv)
if argLength == 4:
    watermark = str(sys.argv[1])
    original = str(sys.argv[2])
    output = str(sys.argv[3])
    encrypt(original, watermark, output)
else :
    if argLength == 3:
        final = Image.open(str(sys.argv[1]))
        output = str(sys.argv[2])
        decrypt(final, output)
    else:
        print "Usage steganoTool :"
        print "To encrypt : steganoTool [evilFileName] [catPicture] [outputFile]"
        print "To decrypt : steganoTool [suspectFile] [outputFile]"
