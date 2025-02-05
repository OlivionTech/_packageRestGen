<?php namespace Olivion\RestGen\Builders;

use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;

class Controller{
    public function __construct(
        private array $params
    ){
        $this->buildParams['ParamService'] = $this->params['service_name'];
        $this->buildParams['ParamController'] = $this->params['controller_name'];
        $this->buildParams['ParamModelLower'] = Str::lower($this->params['model_name']);
        $this->buildParams['ParamModelSnake'] = Str::snake($this->params['model_name'],' ');
        $this->buildParams['ParamModel'] = $this->params['model_name'];
        $this->buildParams['ParamUrl'] = $this->params['crud_url'];
        $this->buildParams['#JwtAuth'] = (env('JWT_SECRET') ? '$this->middleware("auth:api");' : '');
        $this->output = new ConsoleOutput();
        $this->controllerRestDir = base_path().'/app/Http/Controllers/_rest';
        $this->modelBuilder = new Model($this->params);
        $this->controllerRequestOA = [];
        $this->controllerResponseOA = [];
    }

    /**
     * Build open api annotations for method request
     * @param array $fields - model fields
     * @return string $template - generated open api annotations for request
     */
    private function buildOARequest($fields){
        $template = file_get_contents(base_path().'/vendor/olivion/rest-gen/src/Templates/Controllers/OARequest.temp');
        $buildParams = [
            'ParamNameUcfirst' => ucfirst($fields['name']),
            'ParamName' => $fields['name'],
            'ParamIn' => 'query',
            'ParamRequired' => (!empty($fields['rules']['required']) ? 'true' : 'false'),
            'ParamType' => $fields['type'],
            'ParamMaxLength' => (!empty($fields['rules']['max']) ? ',maxLength='.str_replace('max:','',$fields['rules']['max']) : '')
        ];
        foreach($buildParams as $param => $value){
            $template = str_replace($param,$value,$template);
        }
        return $template;
    }

    /**
     * Build open api annotations for method response
     * @param array $fields - model fields
     * @return string $template - generated open api annotations for response
     */
    private function buildOAResponse($fields){
        $template = file_get_contents(base_path().'/vendor/olivion/rest-gen/src/Templates/Controllers/OAResponse.temp');
        $buildParams = [
            'ParamName' => $fields['name'],
            'ParamType' => $fields['type']
        ];
        foreach($buildParams as $param => $value){
            $template = str_replace($param,$value,$template);
        }
        return $template;
    }

    /**
     * Generate request and response open api annotations
     */
    private function oaReqResp(){
        $modelFields = $this->modelBuilder->getFields();
        foreach($modelFields as $column => $fields){
            $this->controllerRequestOA[] = $this->buildOARequest($fields);
            $this->controllerResponseOA[] = $this->buildOAResponse($fields);
        }
    }

    /**
     * Build controller file
     */
    public function build(){
        $template = file_get_contents(base_path().'/vendor/olivion/rest-gen/src/Templates/Controllers/Rest.php');
        foreach($this->buildParams as $param => $value){
            $template = str_replace($param,$value,$template);
        }
        $this->oaReqResp();
        $template = str_replace('#OARequest',join("\n",$this->controllerRequestOA),$template);
        $template = str_replace('#OAResponse',join("\n",$this->controllerResponseOA),$template);
        if(!file_exists($this->controllerRestDir) && !is_dir($this->controllerRestDir)){
            mkdir($this->controllerRestDir);
        }
        file_put_contents(base_path().'/app/Http/Controllers/_rest/'.$this->buildParams['ParamController'].'.php',$template);

        $main_path = base_path().'/app/Http/Controllers/';
        if($this->buildParams['ParamService'])
        {
            if(!file_exists(base_path().'/app/Http/Controllers/'.$this->buildParams['ParamService']) && !is_dir(base_path().'/app/Http/Controllers/'.$this->buildParams['ParamService'])){
                mkdir(base_path().'/app/Http/Controllers/'.$this->buildParams['ParamService']);
            }

            $main_path = base_path().'/app/Http/Controllers/'.$this->buildParams['ParamService'].'/';
        }

        if(!file_exists($main_path.$this->buildParams['ParamController'].'.php')){
            $template = file_get_contents(base_path().'/vendor/olivion/rest-gen/src/Templates/Controllers/Base.php');
            foreach($this->buildParams as $param => $value){
                $template = str_replace($param,$value,$template);
            }
            file_put_contents($main_path.$this->buildParams['ParamController'].'.php',$template);
        }
        $this->output->writeln('<info>Controller '.$this->buildParams['ParamController'].' created successfully</info>');
    }
}