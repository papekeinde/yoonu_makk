from zipfile import ZipFile
from xml.etree import ElementTree as ET
from pathlib import Path

path = Path(r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2).docx')
with ZipFile(path) as z:
    data = z.read('word/numbering.xml')
    root = ET.fromstring(data)
    ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
    print('numbering styles:')
    for num in root.findall('.//w:num', ns):
        numId = num.attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}numId')
        print('num', numId)
        abstractNumId = num.find('w:abstractNumId', ns)
        if abstractNumId is not None:
            print(' abstractNumId', abstractNumId.attrib)
    print('abstracts:')
    for absn in root.findall('.//w:abstractNum', ns):
        absId = absn.attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}abstractNumId')
        print('abs', absId)
        for lvl in absn.findall('.//w:lvl', ns):
            ilvl = lvl.attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}ilvl')
            fmt = lvl.find('w:numFmt', ns)
            txt = lvl.find('w:lvlText', ns)
            print(' level', ilvl, 'numFmt', fmt.attrib if fmt is not None else None, 'lvlText', txt.attrib if txt is not None else None)
            print(ET.tostring(lvl, encoding='unicode'))
            print('---')
