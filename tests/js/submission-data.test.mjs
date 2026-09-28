import test from 'node:test';
import assert from 'node:assert/strict';
import {packSubmissionValues} from '../../src/com_nicode_easy_forms/media/js/submission-data.js';
import {fieldAddress} from '../../src/com_nicode_easy_forms/media/js/field-address.js';

test('repeated multipart addresses keep rows selections and file parts distinct', async () => {
  const group='00000000-0000-4000-8000-000000000001', row='00000000-0000-4000-8000-000000000002', field='00000000-0000-4000-8000-000000000003';
  const key=fieldAddress(field,[{group,instance:row}]), data=new FormData();
  data.set('nef_instances',JSON.stringify({[group]:[row]}));
  data.append(`nef[${key}][]`,' x '); data.append(`nef[${key}][]`,'😀');
  data.append(`nef[${key}][]`,new Blob(['actual bytes']),'row.txt');
  packSubmissionValues(data);
  assert.deepEqual(JSON.parse(data.get('nef_values')),{[key]:[' x ','😀']});
  assert.equal(await data.get(`nef[${key}][]`).text(),'actual bytes');
  assert.deepEqual(JSON.parse(data.get('nef_instances')),{[group]:[row]});
  const invalid=new FormData(); invalid.append(`nef[${group}/${row}]`,'bad');
  const before=[...invalid.entries()];
  assert.throws(()=>packSubmissionValues(invalid),TypeError); assert.deepEqual([...invalid.entries()],before);
});
test('multipart packs 1500 exact values without a PHP variable per field', () => {
  const data = new FormData(); data.set('csrfToken','1'); data.set('attempt','signed');
  for (let i=0;i<1500;i++) data.set(`nef[00000000-0000-4000-8000-${String(i).padStart(12,'0')}]`, `  😀 ${i}  `);
  packSubmissionValues(data); assert.equal([...data.keys()].length,3);
  assert.equal(data.get('csrfToken'),'1'); assert.equal(data.get('attempt'),'signed');
  const values = JSON.parse(data.get('nef_values')); assert.equal(Object.keys(values).length,1500);
  assert.equal(values['00000000-0000-4000-8000-000000001499'],'  😀 1499  ');
});
test('multipart preserves selections files and CAPTCHA', () => {
  const data = new FormData(), uuid='00000000-0000-4000-8000-000000000001', fileId='00000000-0000-4000-8000-000000000002';
  data.append(`nef[${uuid}][]`,' a '); data.append(`nef[${uuid}][]`,'b');
  data.append(`nef[${fileId}]`,new Blob(['fixture']),'fixture.txt'); data.set('easyforms_captcha','answer');
  packSubmissionValues(data);
  assert.deepEqual(JSON.parse(data.get('nef_values')),{[uuid]:[' a ','b']});
  assert.equal(data.get(`nef[${fileId}]`).name,'fixture.txt'); assert.equal(data.get('easyforms_captcha'),'answer');
  const invalid = new FormData(); invalid.append(`nef[${uuid}]`,'a'); invalid.append(`nef[${uuid}][]`,'b');
  assert.throws(()=>packSubmissionValues(invalid),TypeError);
});

test('packing retains all file bytes and order when strings share their multipart name', async () => {
  const uuid='00000000-0000-4000-8000-000000000003';
  for (const suffix of ['', '[]']) {
    const name=`nef[${uuid}]${suffix}`, data=new FormData();
    data.append(name,new Blob(['first bytes']),'first.txt');
    data.append(name,' literal value ');
    data.append(name,new Blob(['second bytes']),'second.txt');
    data.set('csrfToken','1');
    packSubmissionValues(data);
    const files=data.getAll(name);
    assert.deepEqual(files.map(file=>file.name),['first.txt','second.txt']);
    assert.deepEqual(await Promise.all(files.map(file=>file.text())),['first bytes','second bytes']);
    assert.deepEqual(JSON.parse(data.get('nef_values')),{[uuid]:suffix ? [' literal value '] : ' literal value '});
    assert.equal(data.get('csrfToken'),'1');
  }
});

test('ambiguous multiplicity leaves the original multipart payload untouched', () => {
  const uuid='00000000-0000-4000-8000-000000000004', data=new FormData();
  data.append(`nef[${uuid}]`,new Blob(['keep']),'keep.txt');
  data.append(`nef[${uuid}]`,'scalar'); data.append(`nef[${uuid}][]`,'selection');
  const before=[...data.entries()];
  assert.throws(()=>packSubmissionValues(data),TypeError);
  assert.deepEqual([...data.entries()],before);
});
