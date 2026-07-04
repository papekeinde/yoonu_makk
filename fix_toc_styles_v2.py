from pathlib import Path
from tempfile import NamedTemporaryFile
from zipfile import ZipFile, ZIP_DEFLATED
from xml.etree import ElementTree as ET

input_path = Path(r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2).docx')
ns_uri = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
ns = {'w': ns_uri}

with ZipFile(input_path, 'r') as zin:
    names = zin.namelist()
    with NamedTemporaryFile(suffix='.docx', delete=False) as tmpf:
        tmp_path = Path(tmpf.name)

with ZipFile(input_path, 'r') as zin, ZipFile(tmp_path, 'w', compression=ZIP_DEFLATED) as zout:
    for name in zin.namelist():
        content = zin.read(name)
        if name == 'word/styles.xml':
            root = ET.fromstring(content)
            for style in root.findall('.//w:style', ns):
                sid = style.attrib.get('{%s}styleId' % ns_uri)
                if sid not in ('TM1', 'TM2', 'TM3'):
                    continue

                pPr = style.find('w:pPr', ns)
                if pPr is None:
                    pPr = ET.SubElement(style, '{%s}pPr' % ns_uri)

                for tabs_el in pPr.findall('w:tabs', ns):
                    pPr.remove(tabs_el)
                for ind_el in pPr.findall('w:ind', ns):
                    pPr.remove(ind_el)

                tabs = ET.SubElement(pPr, '{%s}tabs' % ns_uri)
                tab = ET.SubElement(tabs, '{%s}tab' % ns_uri)
                tab.set('{%s}val' % ns_uri, 'right')
                tab.set('{%s}leader' % ns_uri, 'dot')
                tab.set('{%s}pos' % ns_uri, '9056')

                if sid == 'TM2':
                    ind = ET.SubElement(pPr, '{%s}ind' % ns_uri)
                    ind.set('{%s}left' % ns_uri, '240')
                elif sid == 'TM3':
                    ind = ET.SubElement(pPr, '{%s}ind' % ns_uri)
                    ind.set('{%s}left' % ns_uri, '480')

            ET.register_namespace('', ns_uri)
            content = ET.tostring(root, encoding='utf-8', xml_declaration=True)

        zout.writestr(name, content)

input_path.replace(tmp_path.with_suffix('.docx'))
