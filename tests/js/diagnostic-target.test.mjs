import test from 'node:test';
import assert from 'node:assert/strict';
import {diagnosticTarget, revealDiagnosticPanel} from '../../src/com_nicode_easy_forms/media/js/diagnostic-target.js';

const draft = {fields:[{uuid:'field-a',validators:[{type:'confirmation'}]}],elements:[{uuid:'element-a'}],rules:[{}],actions:[{}],validators:[{}]};
test('translated diagnostics select an existing locale and resolve indexed fields to stable translation identities', () => {
  const translated = {...draft, translations:{'es-ES':{},'fr-FR':{}}};
  assert.deepEqual(diagnosticTarget('/translations/es-ES/fields/0/config/label',translated),{kind:'translation',locale:'es-ES',path:['fields','field-a','label']});
  assert.deepEqual(diagnosticTarget('/translations/fr-FR/elements/0/text',translated),{kind:'translation',locale:'fr-FR',path:['elements','element-a','text']});
  assert.deepEqual(diagnosticTarget('/translations/es-ES/unknown',translated),{kind:'translation',locale:'es-ES',path:null});
  assert.equal(diagnosticTarget('/translations/de-DE/fields/0/config/label',translated),null);
  assert.equal(diagnosticTarget('/translations/es-ES] input/fields/0/config/label',translated),null);
});
test('diagnostics resolve exact known paths including field validators before the general field target', () => {
  assert.deepEqual(diagnosticTarget('/fields/0/config/label',draft),{kind:'element',uuid:'field-a'});
  assert.deepEqual(diagnosticTarget('/elements/0/parent_uuid',draft),{kind:'element',uuid:'element-a'});
  for (const [path,selector] of [['/rules/0','[data-nef-logic-panel]'],['/actions/0','[data-nef-actions-panel]'],['/validators/0','[data-nef-validator-panel]'],['/fields/0/validators/0','[data-nef-validator-panel]']]) {
    assert.deepEqual(diagnosticTarget(path+'/config',draft),{kind:'panel',selector,path});
  }
  for (const path of ['/fields/0oops','/rules/2','/actions/-1','/unknown/0','/actions/0] input',null]) assert.equal(diagnosticTarget(path,draft),null);
});
test('locating a closed card waits for editor toggle redraw and then focuses the new control', () => {
  const events=[], focused=[], frames=[];
  let cards=[];
  const panel={open:false,addEventListener:(name,callback,options)=>{assert.equal(name,'toggle');assert.equal(options.once,true);events.push(callback);},querySelectorAll:()=>cards,querySelector:()=>({focus:()=>focused.push('summary')})};
  const root={querySelector:()=>panel};
  revealDiagnosticPanel(root,{selector:'[data-nef-actions-panel]',path:'/actions/0'},callback=>frames.push(callback));
  assert.equal(panel.open,true); assert.deepEqual(focused,[]);
  cards=[{dataset:{nefDiagnosticPath:'/actions/0'},querySelector:()=>({focus:()=>focused.push('new-control')})}];
  events[0](); assert.deepEqual(focused,[]); frames.shift()(); assert.deepEqual(focused,['new-control']);
  revealDiagnosticPanel(root,{selector:'[data-nef-actions-panel]',path:'/actions/0'},callback=>frames.push(callback)); frames.shift()();
  assert.deepEqual(focused,['new-control','new-control']);
  revealDiagnosticPanel(root,{selector:'[data-nef-actions-panel]',path:'/actions/99'},callback=>frames.push(callback)); frames.shift()();
  assert.equal(focused.at(-1),'summary');
});
