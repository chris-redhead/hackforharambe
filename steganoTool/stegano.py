from PIL import Image, ImageMath
import sys

def encrypt(original, watermark, output):
    red, green, blue, alpha = original.split()
    wred, wgreen, wblue, walpha = watermark.split()

    red2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = red, b = wred)
    green2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = green, b = wgreen)
    blue2 = ImageMath.eval("convert(a&0xFE|b&0x1, 'L')", a = blue, b = wblue)

    out = Image.merge("RGB", (red2, green2, blue2))
    out.save(output)

def decrypt(water, output): 
    red, green, blue = water.split()
    watermark = ImageMath.eval("(a&0x1)*255", a = blue)
    #watermark = ImageMath.eval("(a&0x1)*255", a = green)
    #watermark = ImageMath.eval("(a&0x1)*255", a = red)
    watermark = watermark.convert("L")
    watermark.save(output)

#main
argLength = len(sys.argv)
if argLength == 4:
    watermark = Image.open(str(sys.argv[1]))
    original = Image.open(str(sys.argv[2]))
    watermark = watermark.resize(original.size)
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
