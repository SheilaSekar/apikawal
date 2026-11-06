import { readFile, access } from 'node:fs/promises';
import { readdir } from 'node:fs/promises';
import { join } from 'node:path';
const root=new URL('../',import.meta.url);
const files=(await readdir(root)).filter(name=>name.endsWith('.html'));
const failures=[];
for(const name of files){
  const html=await readFile(new URL(name,root),'utf8');
  for(const [label,ok] of [['lang id',/<html[^>]+lang="id"/i.test(html)],['viewport',/<meta[^>]+name="viewport"/i.test(html)],['skip link',/class="skip-link" href="#main"/.test(html)],['main landmark',/<main\b[^>]*id="main"/.test(html)],['shared shell',/data-site-chrome/.test(html)],['noscript emergency fallback',/<noscript>[\s\S]*?Hubungi 112/.test(html)]]) if(!ok) failures.push(`${name}: missing ${label}`);
  for(const match of html.matchAll(/(?:src|href)="(assets\/(?:css|js|images|vendor)\/[^"#?]+)"/g)){try{await access(new URL(match[1],new URL(name,root)));}catch{failures.push(`${name}: missing local asset ${match[1]}`);}}
  if(/<script(?![^>]*type="module")[^>]*>/.test(html)) failures.push(`${name}: script must use an external ES module`);
}
if(failures.length){console.error(failures.join('\n'));process.exitCode=1;}else console.log(`Static shell check passed for ${files.length} pages and all referenced local assets.`);
