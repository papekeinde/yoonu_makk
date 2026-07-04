from zipfile import ZipFile
from xml.etree import ElementTree as ET
from pathlib import Path

path = Path(r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2).docx')
with ZipFile(path) as z:
    data = z.read('word/styles.xml')
    root = ET.fromstring(data)
    ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
    print('styles count', len(root.findall('.//w:style', ns)))
    for style in root.findall('.//w:style', ns):
        sid = style.attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}styleId')
        if sid in ['Titre1','Titre2','Titre3','Titre4','TM1','TM2','TM3','Normal']:
            print('--- style', sid, '---')
            print(ET.tostring(style, encoding='unicode'))
