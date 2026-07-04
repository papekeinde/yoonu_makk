from pathlib import Path
import shutil
from docx import Document
from docx.shared import Inches, Pt
from docx.enum.text import WD_TAB_ALIGNMENT, WD_TAB_LEADER

src = Path(r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2)_backup.docx')
dst = Path(r'C:\Users\dell\Downloads\Squelette Yoonu Jigueen (2).docx')
shutil.copy2(src, dst)

doc = Document(dst)
styles = {
    'toc 1': (0, 6.0),
    'toc 2': (0.24, 6.0),
    'toc 3': (0.48, 6.0),
}

for name, (left, right_pos) in styles.items():
    style = doc.styles[name]
    pf = style.paragraph_format
    pf.left_indent = Inches(left)
    pf.space_after = Pt(2)
    pf.tab_stops.clear_all()
    pf.tab_stops.add_tab_stop(Inches(right_pos), WD_TAB_ALIGNMENT.RIGHT, WD_TAB_LEADER.DOTS)
    style.font.name = 'Times New Roman'
    style.font.size = Pt(12)

doc.save(dst)
print('saved', dst, 'size', dst.stat().st_size)
