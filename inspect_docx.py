from zipfile import ZipFile
from xml.etree import ElementTree as ET
import os

path = r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2).docx'
print('exists', os.path.exists(path))
with ZipFile(path) as z:
    names = z.namelist()
    print('entries', len(names))
    for n in names:
        if n.endswith('document.xml') or n.endswith('styles.xml') or n.endswith('numbering.xml') or n.endswith('toc.xml') or n.startswith('word/_rels') or n.startswith('word/header') or n.startswith('word/footer'):
            print(n)

    data = z.read('word/document.xml')
    root = ET.fromstring(data)
    ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}

    def iter_paragraphs(root):
        for p in root.findall('.//w:p', ns):
            yield p

    print('paragraphs:')
    for i, p in enumerate(iter_paragraphs(root), 1):
        texts = []
        for t in p.findall('.//w:t', ns):
            if t.text:
                texts.append(t.text)
        text = ''.join(texts).strip()
        if text:
            print(i, repr(text))
            # print style ids if any
            pPr = p.find('w:pPr', ns)
            if pPr is not None:
                style = pPr.find('w:pStyle', ns)
                if style is not None:
                    print('   style', style.attrib)
                for el in pPr.findall('.//w:tabs', ns):
                    print('   tabs', el.attrib)
            # print field codes if any
            for instr in p.findall('.//w:instrText', ns):
                print('   instr', instr.text)

    # print styles with names
    try:
        styles_data = z.read('word/styles.xml')
        styles_root = ET.fromstring(styles_data)
        print('styles:')
        for style in styles_root.findall('.//w:style', ns):
            style_id = style.attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}styleId')
            name = None
            if style.find('w:name', ns) is not None:
                name = style.find('w:name', ns).attrib.get('{http://schemas.openxmlformats.org/wordprocessingml/2006/main}val')
            if style_id or name:
                print(style_id, name)
    except Exception as e:
        print('styles read error', e)
