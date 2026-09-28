<?php
declare(strict_types=1);

/** Isolated fixture services; no production Joomla bootstrap or external actions. */
function repeatedUploadServices(bool $rejectCaptcha = false, ?Nicode\EasyForms\Contract\MailTransportInterface $mail = null): array
{
    $root=dirname(__DIR__);
    if(!defined('_JEXEC')) { define('_JEXEC',1); }
    require_once $root.'/build/joomla-6.0.0/libraries/vendor/autoload.php';
    require_once $root.'/src/lib_nicode_easy_forms/autoload.php';
    $config=json_decode(ltrim(file_get_contents($root.'/build/database-test.json'),"\xEF\xBB\xBF"),true,flags:JSON_THROW_ON_ERROR);
    if($config['host']!=='127.0.0.1' || $config['port']!==13367 || $config['database']!=='easyforms_test') { throw new RuntimeException('Non-isolated upload database.'); }
    $driver=(new Joomla\Database\DatabaseFactory())->getDriver('mysql',['host'=>$config['host'],'port'=>$config['port'],'user'=>$config['user'],'password'=>$config['password'],'database'=>$config['database'],'prefix'=>'nef_','charset'=>'utf8mb4']);
    $db=new Nicode\EasyForms\Infrastructure\Database\Connection($driver);
    $auth=json_decode(ltrim(file_get_contents($root.'/build/upload-test.json'),"\xEF\xBB\xBF"),true,flags:JSON_THROW_ON_ERROR); $key=hash('sha256',$auth['nonce']);
    $types=new Nicode\EasyForms\Registry\FieldTypeRegistry(); Nicode\EasyForms\Field\CoreFieldTypes::register($types);
    $actions=new Nicode\EasyForms\Registry\ActionRegistry();
    if ($mail!==null) { $actions->register(new Nicode\EasyForms\Actions\EmailAction($mail,new Nicode\EasyForms\Actions\TokenTemplate())); }
    $compiler=new Nicode\EasyForms\Compiler\FormCompiler($types,$actions,new Nicode\EasyForms\Registry\ProviderRegistry(),new Nicode\EasyForms\Registry\ProviderRegistry());
    $forms=new Nicode\EasyForms\Infrastructure\Database\FormRepository($db,$compiler);
    $jobs=new Nicode\EasyForms\Infrastructure\Database\JobRepository($db); $journal=new Nicode\EasyForms\Infrastructure\Database\UploadJournal($db,$jobs);
    $directory=$root.'/build/repeated-upload-private'; if(!is_dir($directory)) { mkdir($directory,0700,true); }
    $storage=new Nicode\EasyForms\Storage\LocalStorage($directory,$root.'/tests/http');
    $providers=new Nicode\EasyForms\Registry\StorageProviderRegistry(); $providers->register($storage);
    $submissions=new Nicode\EasyForms\Infrastructure\Database\SubmissionRepository($db,new Nicode\EasyForms\Search\IndexProjector($types),$key,$journal);
    $conditions=new Nicode\EasyForms\Rules\ConditionEvaluator(Nicode\EasyForms\Registry\RuleOperatorRegistry::core());
    $validation=new Nicode\EasyForms\Validation\ValidationEngine($types,new Nicode\EasyForms\Rules\RuleEngine($conditions,Nicode\EasyForms\Registry\RuleEffectRegistry::core(),$types));
    $captcha=new class($rejectCaptcha) implements Nicode\EasyForms\Contract\CaptchaAdapterInterface {
        public function __construct(private bool $reject) {}
        public function available():array { return []; }
        public function assertAvailable(Nicode\EasyForms\Security\CaptchaPolicy $policy):void {}
        public function render(Nicode\EasyForms\Security\CaptchaPolicy $policy,string $instance):string { return ''; }
        public function validate(Nicode\EasyForms\Security\CaptchaPolicy $policy,?string $answer):void { if($this->reject) { throw new Nicode\EasyForms\Security\CaptchaException('captcha_error'); } }
    };
    $attempts=new Nicode\EasyForms\Security\AttemptTokens($key);
    $pipeline=new Nicode\EasyForms\Application\SubmissionPipeline($forms,$submissions,new Nicode\EasyForms\Security\PublicAccess(),$attempts,$captcha,new Nicode\EasyForms\Infrastructure\Database\RateLimiter($db),$validation,new Nicode\EasyForms\Actions\ActionEngine($actions,new Nicode\EasyForms\Infrastructure\Database\ActionRunRepository($db),$conditions,$types),new Nicode\EasyForms\Submission\PostSubmit($conditions,$types,new Nicode\EasyForms\Actions\TokenTemplate(),new Nicode\EasyForms\Security\RedirectPolicy()),$providers,new Nicode\EasyForms\Storage\HttpUploadGateway(new Nicode\EasyForms\Storage\UploadInspector(),$storage,journal:$journal),uploadJournal:$journal);
    return compact('db','forms','submissions','storage','attempts','pipeline');
}
