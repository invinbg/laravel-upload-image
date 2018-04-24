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

    /**
     * Upload constructor.
     * @param array $files          上传的文件
     * @param array $extensions     允许的后缀
     */
    public function __construct($files = [], array $extensions = [])
    {
        if (! $files) {
            $files = request()->file();
        }
        $this->files = $files instanceof UploadedFile ? collect([$files]) : collect($files);
        $this->paths = collect([]);
        $this->extension($extensions);
    }

    /**
     * @param string $func_name     方法名只允许为file
     * @param $arguments
     * @return Upload
     * @throws UploadException
     */
    public static function __callStatic($func_name = '', $arguments)
    {
        if($func_name === 'file')
        {
            $files = isset($arguments[0]) ? $arguments[0] : [];

            return new self($files);
        }
        throw new UploadException($func_name.'方法不存在');
    }

    /**
     * 允许上传文件的后缀
     * @param array $extensions     后缀名
     * @return $this
     */
    public function extension(array $extensions = [])
    {
        $this->extensions = $extensions ?: $this->extensions;
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
        $this->isValid();

        $this->check_extenstion();

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
        if ($this->files->isEmpty()) {
            throw new UploadException('请上传图片');
        }

        $this->files->map(function($file){
            if(! $file->isValid() )
            {
                throw new UploadException('文件在上传过程中发生了错误');
            }
        });
    }

    /**
     * clone
     * @return $this
     */
    protected function file()
    {
        return $this;
    }
}