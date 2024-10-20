(()=>{"use strict";var e,o={309:(e,o,t)=>{const i=window.wp.blocks,r=window.wp.apiFetch;var a=t.n(r);const s=window.wp.components,n=window.wp.element,l=window.wp.i18n,d=window.wp.notices,c=window.wp.blockEditor,p=window.wp.coreData,m=window.ReactJSXRuntime,b=e=>{const{formSettings:o,handleSettingChange:t}=e;return(0,m.jsx)(m.Fragment,{children:o&&Object.entries(o).map((([e,o])=>"select"===o.type?(0,m.jsx)(s.SelectControl,{label:o.label,value:o.value,options:o.options,onChange:o=>t(e,o)},e):"radio"===o.type?(0,m.jsx)(s.RadioControl,{label:o.label,selected:o.value,options:o.options,onChange:o=>t(e,o)},e):"toggle"===o.type?(0,m.jsx)(s.ToggleControl,{label:o.label,checked:o.value,onChange:o=>t(e,o)},e):(0,m.jsx)(s.TextControl,{label:o.label,value:o.value,onChange:o=>t(e,o)},e)))})},f=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"obsidian-form/form","version":"0.1.0","title":"Obsidian Form","category":"widgets","icon":"feedback","description":"An Obsidian Form block.","supports":{"html":false,"align":true,"color":{"background":true,"text":true},"anchor":true},"attributes":{"anchor":{"type":"string","default":""},"formPostId":{"type":"number","default":0},"formSettings":{"type":"object","default":{"id":{"label":"Form ID","type":"string","value":""},"title":{"label":"Form Title","type":"string","value":""},"className":{"label":"Form Class","type":"string","value":""},"description":{"label":"Form Description","type":"string","value":""},"labelPlacement":{"label":"Field Label Placement","type":"select","value":"top","options":[{"label":"Top","value":"top"},{"label":"Left","value":"left"},{"label":"Bottom","value":"bottom"}]},"descriptionPlacement":{"label":"Field Description Placement","type":"radio","value":"bottom","options":[{"label":"Top","value":"top"},{"label":"Bottom","value":"bottom"}]},"globalHasPlaceholder":{"label":"Fields Have Placeholders","type":"toggle","value":true},"validationPlacement":{"label":"Validation Placement","type":"radio","value":"bottom","options":[{"label":"Top","value":"top"},{"label":"Bottom","value":"bottom"}]},"requiredIndicator":{"label":"Required Indicator","type":"string","value":"*"}}}},"editorScript":"file:./index.js","editorStyle":"file:./index.css","script":"file:./view.js","style":"file:./style-index.css","allowedBlocks":["obsidian-form/field-group"],"providesContext":{"obsidian-form/formSettings":"formSettings"},"render":"file:./index.php"}');(0,i.registerBlockType)(f.name,{...f,edit:function(e){const{attributes:o,setAttributes:t}=e,{formPostId:i,formSettings:r}=o,[f,u]=(0,n.useState)(!1),[h,g]=(0,n.useState)(""),[v,w]=(0,n.useState)(null),[y,x]=(0,n.useState)([]),[_,j]=(0,n.useState)(!0),[C,P]=(0,n.useState)(!1),[k,S,O]=(0,p.useEntityBlockEditor)("postType","obsidian_form",{id:i||0}),[B,F]=(0,p.useEntityProp)("postType","obsidian_form","title",i||0),T=(0,c.useBlockProps)({className:"obsidian-form__editor obsidian-form__editor--ready"}),I=(0,c.useInnerBlocksProps)({},{value:k,onInput:S,onChange:O,allowedBlocks:["obsidian-form/field-group"],renderAppender:k?.length?void 0:c.InnerBlocks.ButtonBlockAppender});(0,n.useEffect)((()=>{i||(j(!0),(async()=>{let e=1,o=[],t=1;for(;e<=t;)try{const i=await a()({path:`/wp/v2/obsidian_form?per_page=100&page=${e}`,parse:!1}),r=await i.json();if(!(r.length>0))break;o=o.concat(r),t=parseInt(i.headers.get("X-WP-TotalPages"),10),e++}catch(e){console.error("Error fetching forms:",e),(0,d.createNotice)("error",(0,l.__)("Error fetching forms. Please try again.","obsidian-forms"),{isDismissible:!0});break}return o})().then((e=>{if(0===e.length)P(!0);else{const o=e.map((e=>({label:`${e.title.rendered} (ID: ${e.id})`,value:e.id})));x(o),P(!1)}})).finally((()=>{j(!1)})))}),[i]);return i?(0,m.jsxs)(m.Fragment,{children:[(0,m.jsx)(c.InspectorControls,{children:(0,m.jsx)(s.PanelBody,{header:"Obsidian Form Settings",children:(0,m.jsx)(b,{formSettings:r,handleSettingChange:(e,o)=>{const i={...r,[e]:{...r[e],value:o}};t({formSettings:i})}})})}),(0,m.jsxs)("form",{...T,children:[(0,m.jsx)(c.RichText,{value:B,onChange:F,placeholder:(0,l.__)("Enter Form Title","obsidian-forms"),tagName:"h2"}),(0,m.jsx)("div",{className:"wp-block-obsidian-form-fields",children:(0,m.jsx)("div",{...I})})]})]}):(0,m.jsx)(s.Placeholder,{icon:"feedback",label:(0,l.__)("Obsidian Form","obsidian-forms"),instructions:C?(0,l.__)("Create a new form to get started!","obsidian-forms"):(0,l.__)("Create a new form or select an existing one.","obsidian-forms"),children:(0,m.jsxs)("div",{className:"obsidian-forms-choice",children:[(0,m.jsxs)("div",{className:"obsidian-forms-choice__form-creator",children:[(0,m.jsx)(s.TextControl,{placeholder:(0,l.__)("New form title…","obsidian-forms"),value:h,onChange:g}),(0,m.jsx)(s.Button,{isPrimary:!0,onClick:async()=>{if(h.trim()){const e=await a()({path:"/wp/v2/obsidian_form",method:"POST",data:{title:h,content:'\x3c!-- wp:obsidian-form/field-group --\x3e\n\x3c!-- wp:obsidian-form/field {"isRequired":true} /--\x3e\n\x3c!-- /wp:obsidian-form/field-group --\x3e',status:"publish"}});e?.id?(t({formPostId:e.id}),u(!1)):(0,d.createNotice)("error",(0,l.__)("Error creating form. Please try again.","obsidian-forms"),{isDismissible:!0})}},disabled:!h.trim(),children:(0,l.__)("Create","obsidian-forms")})]}),!C&&(0,m.jsxs)("div",{className:"obsidian-forms-choice__form-selector",children:[(0,m.jsx)(s.ComboboxControl,{value:v,options:y,onChange:e=>w(e),shouldShowMenuOnFocus:!0,disabled:_}),(0,m.jsxs)("div",{className:"button-group",children:[(0,m.jsx)(s.Button,{isPrimary:!0,onClick:()=>{v&&t({formPostId:v})},disabled:!v||_,children:(0,l.__)("Select","obsidian-forms")}),(0,m.jsx)(s.Button,{className:"is-secondary",onClick:async()=>{if(v)try{const e=await a()({path:`/wp/v2/obsidian_form/${v}`});if(e?.id){const o=await a()({path:"/wp/v2/obsidian_form",method:"POST",data:{title:`${e.title.rendered} (Copy)`,content:e.raw_content,status:"publish"}});o?.id&&t({formPostId:o.id})}}catch(e){console.error("Error copying form:",e),(0,d.createNotice)("error",(0,l.__)("Error copying form. Please try again.","obsidian-forms"),{isDismissible:!0})}},disabled:!v||_,children:(0,l.__)("Copy","obsidian-forms")})]})]})]})})},save:()=>null})}},t={};function i(e){var r=t[e];if(void 0!==r)return r.exports;var a=t[e]={exports:{}};return o[e](a,a.exports,i),a.exports}i.m=o,e=[],i.O=(o,t,r,a)=>{if(!t){var s=1/0;for(c=0;c<e.length;c++){t=e[c][0],r=e[c][1],a=e[c][2];for(var n=!0,l=0;l<t.length;l++)(!1&a||s>=a)&&Object.keys(i.O).every((e=>i.O[e](t[l])))?t.splice(l--,1):(n=!1,a<s&&(s=a));if(n){e.splice(c--,1);var d=r();void 0!==d&&(o=d)}}return o}a=a||0;for(var c=e.length;c>0&&e[c-1][2]>a;c--)e[c]=e[c-1];e[c]=[t,r,a]},i.n=e=>{var o=e&&e.__esModule?()=>e.default:()=>e;return i.d(o,{a:o}),o},i.d=(e,o)=>{for(var t in o)i.o(o,t)&&!i.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:o[t]})},i.o=(e,o)=>Object.prototype.hasOwnProperty.call(e,o),(()=>{var e={884:0,36:0};i.O.j=o=>0===e[o];var o=(o,t)=>{var r,a,s=t[0],n=t[1],l=t[2],d=0;if(s.some((o=>0!==e[o]))){for(r in n)i.o(n,r)&&(i.m[r]=n[r]);if(l)var c=l(i)}for(o&&o(t);d<s.length;d++)a=s[d],i.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return i.O(c)},t=self.webpackChunkobsidian_forms=self.webpackChunkobsidian_forms||[];t.forEach(o.bind(null,0)),t.push=o.bind(null,t.push.bind(t))})();var r=i.O(void 0,[36],(()=>i(309)));r=i.O(r)})();