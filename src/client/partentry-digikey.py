#!/usr/bin/env python

# arg 1 is quantity
# arg 2 is is digikey part number | digikey part page url


urltemplate = "http://hollabaugh.com/electronicparts.php?%s"

import lxml.html
from lxml.etree import tostring
import sys, traceback, urllib


if len(sys.argv) == 1: # run from scanner
    q = raw_input("quantity? ")
    partinfo = raw_input("part num? ")
    doc = lxml.html.parse("http://www.digikey.com/product-search/en?lang=en&site=US&keywords=%s"%partinfo)
else:
    if len(sys.argv) == 3: # do a search 
        partinfo = sys.argv[2] 
        if partinfo.lower().count("http") == 1:
            doc = lxml.html.parse(partinfo)
        else:
            doc = lxml.html.parse("http://www.digikey.com/product-search/en?lang=en&site=US&keywords=%s"%partinfo)
        q  = sys.argv[1]
    else:
        print "usage partsentry quantity digikeyPartNumber|digikeyPartPageUrl"
        sys.exit(1)

try:
    table = doc.xpath("/html//*[@class='product-details']")[0]
# print tostring(table)
except IndexError:
    urls = doc.xpath("/html//*/td[@class='digikey-partnumber']")
    for url in urls:
        a = url.xpath(".//a")[0]
        if a.text_content().count('1-ND') or a.text_content().count('T-ND'):
            if a.get('href').count(partinfo.upper()):
                newurl = 'http://www.digikey.com/'+a.get('href')
                doc = lxml.html.parse(newurl)
                table = doc.xpath("/html//*[@class='product-details']")[0]
                break
except:
    traceback.print_exc(file=sys.stdout)
    sys.exit(1)



manu = table.xpath(".//*[@itemprop='manufacturer']")[0].text_content()
if manu.split(" ")[0] == "Texas":
    manu = "TI"
#print tostring(id)
#print manu.text_content()

manupart = table.xpath(".//*[@itemprop='model']")[0].text_content()
#print tostring(id)
#print manupart.text_content()

desc = table.xpath(".//*[@itemprop='description']")[0].text_content()
#print tostring(id)
#print desc.text_content()

dkpart = table.xpath(".//*[@id='reportpartnumber']")[0].text_content()
#print tostring(id)
#print dkpart.text_content()

try:
    dkprice1 = table.xpath(".//*[@id='pricing']//td")[1].text_content()
    priceinfo = "            %s $%0.2f@%s = $%0.2f"%(dkpart,float(dkprice1),q,float(dkprice1)*float(q))
except:
    priceinfo = ""

out = "%s,%s,%s,%s,1,%s"%(q,manu,manupart,desc,dkpart)

y = raw_input(out+priceinfo+"\nY/n? ")
if y != 'n':
    url= urltemplate%"action=add&quantity=%s&manufacturer=%s&manufacturerpart=%s&description=%s&distributor=%s&distributorpart=%s&location=%s"%(q,manu,manupart,desc,"digikey",dkpart,"inventory")
    print url
    rv = urllib.urlopen(url)
    print rv.read()


