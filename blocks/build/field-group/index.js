(()=>{"use strict";var e,o={502:()=>{const e=window.wp.blocks,o=window.wp.blockEditor,i=window.wp.data,r=window.wp.components,s=window.wp.primitives,n=window.ReactJSXRuntime,t=(0,n.jsx)(s.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",children:(0,n.jsx)(s.Path,{d:"M2 12C2 6.44444 6.44444 2 12 2C17.5556 2 22 6.44444 22 12C22 17.5556 17.5556 22 12 22C6.44444 22 2 17.5556 2 12ZM13 11V7H11V11H7V13H11V17H13V13H17V11H13Z"})}),l=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"obsidian-form/field-group","version":"0.1.0","title":"Field Group","category":"widgets","icon":"feedback","description":"An Obsidian Field Group block.","supports":{"html":false,"align":true},"attributes":{},"editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css","parent":["obsidian-form/form"],"allowedBlocks":["obsidian-form/field"]}');(0,e.registerBlockType)(l.name,{...l,edit:s=>{const{clientId:l}=s,d=()=>{const o=(0,i.select)("core/block-editor").getBlocksByClientId(l)[0].innerBlocks.length,r=(0,e.createBlock)("obsidian-form/field");(0,i.dispatch)("core/block-editor").insertBlock(r,o,l)},c=(0,o.useBlockProps)(),a=(0,o.useInnerBlocksProps)({},{allowedBlocks:["obsidian-form/field"],template:[["obsidian-form/field"]],renderAppender:()=>(0,n.jsx)(r.Icon,{icon:t,className:"wp-block-obsidian-form-field-group__add-field",onClick:d})});return(0,n.jsx)("div",{...c,children:(0,n.jsx)("div",{...a})})},save:()=>(0,n.jsx)(o.InnerBlocks.Content,{})})}},i={};function r(e){var s=i[e];if(void 0!==s)return s.exports;var n=i[e]={exports:{}};return o[e](n,n.exports,r),n.exports}r.m=o,e=[],r.O=(o,i,s,n)=>{if(!i){var t=1/0;for(a=0;a<e.length;a++){i=e[a][0],s=e[a][1],n=e[a][2];for(var l=!0,d=0;d<i.length;d++)(!1&n||t>=n)&&Object.keys(r.O).every((e=>r.O[e](i[d])))?i.splice(d--,1):(l=!1,n<t&&(t=n));if(l){e.splice(a--,1);var c=s();void 0!==c&&(o=c)}}return o}n=n||0;for(var a=e.length;a>0&&e[a-1][2]>n;a--)e[a]=e[a-1];e[a]=[i,s,n]},r.o=(e,o)=>Object.prototype.hasOwnProperty.call(e,o),(()=>{var e={922:0,534:0};r.O.j=o=>0===e[o];var o=(o,i)=>{var s,n,t=i[0],l=i[1],d=i[2],c=0;if(t.some((o=>0!==e[o]))){for(s in l)r.o(l,s)&&(r.m[s]=l[s]);if(d)var a=d(r)}for(o&&o(i);c<t.length;c++)n=t[c],r.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return r.O(a)},i=self.webpackChunkobsidian_forms=self.webpackChunkobsidian_forms||[];i.forEach(o.bind(null,0)),i.push=o.bind(null,i.push.bind(i))})();var s=r.O(void 0,[534],(()=>r(502)));s=r.O(s)})();