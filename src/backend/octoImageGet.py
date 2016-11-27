#!/usr/bin/env python

import MySQLdb as mdb
import sys,urllib2,re,time,urllib

'''
		<meta property="og:image" content="http://sigma.octopart.com/9873486/image/Texas-Instruments-LP3985IM5-3.3-NOPB.jpg" />
'''
ex = re.compile(r'.*"og:image" content="(.*?)"',re.MULTILINE)

try:
    con = mdb.connect('localhost', 'inventory', 'inventory7', 'inventory');
    cur = con.cursor(mdb.cursors.DictCursor)
    cur.execute("SELECT * FROM electronicparts WHERE LENGTH(octoparturl) > 0 and LENGTH(image) = 0 limit 10")
    rows = cur.fetchall()
    for row in rows:
        print row['id'],row['partnumber'],row['octoparturl']
        url = row['octoparturl']
        octopage = urllib2.urlopen(url).read()
        m = ex.search(octopage)
        try:
            imageurl = m.groups()[0]
            print imageurl
            fn = urllib.unquote(imageurl.split('/')[-1])

            req = urllib2.Request(imageurl, headers={ 'User-Agent': 'Mozilla/5.0' })
            imagefile = urllib2.urlopen(req).read()
            open("inventoryimages/"+fn,"w").write(imagefile)
            cur.execute('UPDATE electronicparts SET image = "'+fn+'" WHERE id = %d'%row['id'])
        except:
            cur.execute('UPDATE electronicparts SET image = "icon_octopart_blank.png" WHERE id = %d'%row['id'])
            print "no image"
        print
        time.sleep(5)


except mdb.Error, e:
    print "Error %d: %s" % (e.args[0],e.args[1])
    sys.exit(1)
    
finally:    
    if con:    
        con.close()
