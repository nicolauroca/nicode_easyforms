<?php
declare(strict_types=1);

$root=dirname(__DIR__); $fixture=$root.'/build/status-generator-'.bin2hex(random_bytes(5));
mkdir($fixture.'/tools',0777,true); mkdir($fixture.'/docs');
copy($root.'/tools/status.php',$fixture.'/tools/status.php');
$spec="**FORM-001** Forms.\n**A11Y-001** Accessibility.\n";
$catalog=['FORM-001'=>['status'=>'COMPLETE','implementation'=>'Implemented forms.','evidence'=>'tests/forms.php'], 'A11Y-001'=>['status'=>'PENDING','implementation'=>'Not implemented; entire acceptance scope remains.','evidence'=>'No acceptance evidence']];
$document="# Status\n\nComplete requirements: 0.\n\n| FORM-001 | Old | PENDING | old | old |\n\nTotal: stale\n\nKeep this independent release qualification.\n";
$reset=static function()use($fixture,$spec,$catalog,$document):void {
    file_put_contents($fixture.'/docs/29_REQUIREMENTS_TRACEABILITY.md',$spec);
    file_put_contents($fixture.'/docs/requirements-status.json',json_encode($catalog,JSON_THROW_ON_ERROR));
    file_put_contents($fixture.'/docs/IMPLEMENTATION_STATUS.md',$document);
};
$run=static function(bool $check=false)use($fixture):int {
    $command=[PHP_BINARY,$fixture.'/tools/status.php']; if($check) { $command[]='--check'; }
    $process=proc_open($command,[0=>['pipe','r'],1=>['file',$fixture.'/output.log','w'],2=>['file',$fixture.'/error.log','w']],$pipes,$fixture);
    if(!is_resource($process)) { throw new RuntimeException('Cannot run status fixture.'); }
    fclose($pipes[0]); return proc_close($process);
};
$reset();
if($run(true)===0 || file_get_contents($fixture.'/docs/IMPLEMENTATION_STATUS.md')!==$document) { throw new RuntimeException('Check mode failed to detect drift without writing.'); }
if($run()!==0 || $run(true)!==0) { throw new RuntimeException('Status regeneration/check failed.'); }
$rendered=file_get_contents($fixture.'/docs/IMPLEMENTATION_STATUS.md');
if(!str_contains($rendered,'2 requirements; 0 partially implemented; 1 pending; 1 completed') || !str_contains($rendered,'| A11Y-001 | Accessibility. | PENDING |') || !str_contains($rendered,'Keep this independent release qualification.')) { throw new RuntimeException('Status totals, alphanumeric ID or independent prose lost.'); }
foreach(['missing','extra','state','evidence','table','duplicate-spec'] as $failure) {
    $reset(); $bad=$catalog;
    switch($failure) {
        case 'missing': unset($bad['A11Y-001']); break;
        case 'extra': $bad['FORM-999']=$bad['FORM-001']; break;
        case 'state': $bad['FORM-001']['status']='DONE'; break;
        case 'evidence': $bad['FORM-001']['evidence']='No acceptance evidence'; break;
        case 'table': $bad['FORM-001']['implementation']='Unexpected | cell'; break;
        case 'duplicate-spec': file_put_contents($fixture.'/docs/29_REQUIREMENTS_TRACEABILITY.md',$spec."**FORM-001** Duplicate.\n"); break;
    }
    file_put_contents($fixture.'/docs/requirements-status.json',json_encode($bad,JSON_THROW_ON_ERROR));
    if($run()===0 || file_get_contents($fixture.'/docs/IMPLEMENTATION_STATUS.md')!==$document) { throw new RuntimeException('Malformed catalog wrote a status report: '.$failure); }
}
echo "Status generator: exact IDs, preserved prose, counts, read-only drift check and six invalid catalog cases passed.\n";
