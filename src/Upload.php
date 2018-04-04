<?php
namespace InviNBG\UploadImage;

use Illuminate\Http\UploadedFile;
use InviNBG\UploadImage\Exceptions\UploadException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Upload {

    public $extensions = [
        'png','jpg','jpeg','gif'
    ];
    protected $files;
    protected $paths;

    public function __construct(Collection $files)
    {
        $this->files = $files;
        $this->paths = collect([]);
    }

    public static function __callStatic($func_name = '',$arguments)
    {
        if($func_name === 'file')
        {
            if (! $arguments) {
                self::file(request()->file());
            }

            if($arguments[0] instanceof UploadedFile)
            {
                $arguments[0] = [$arguments[0]];
            }

            $class = new self(collect($arguments[0]));
            return $class->file();
        }
        throw new UploadException($func_name.'方法不存在');
    }

    protected function file()
    {
        $this->isValid();
        $this->check_extenstion();
        return $this;
    }

    /**
     * 上传
     * @param $path
     * @param array $options
     * @return false|string
     */
    public function store($path, $options = [])
    {
        $dir = Carbon::now()->format('Ymd');
        $this->files->map(function($file) use($path, $options, $dir){
            $this->paths->push($file->store($path.'/'.$dir, $options));
        });
        return $this->paths;
    }

    /**
     * 验证文件后缀
     * @throws UploadException
     */
    protected function check_extenstion()
    {

        $this->files->map(function($file){
            if (! in_array($file->extension(), $this->extensions) )
            {
                throw new UploadException('文件类型不允许');
            }
        });

    }

    /**
     * 上传过程中是否发生错误
     * @throws UploadException
     */
    protected function isValid()
    {
        $this->files->map(function($file){
            if(! $file->isValid() )
            {
                throw new UploadException('文件在上传过程中发生了错误');
            }
        });
    }
}