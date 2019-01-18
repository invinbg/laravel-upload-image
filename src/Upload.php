<?php
namespace InviNBG\UploadImage;

use Illuminate\Http\UploadedFile;
use InviNBG\UploadImage\Exceptions\UploadException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Upload {

    /**
     * 允许上传文件后缀
     * @var array
     */
    public $extensions = [
        'png','jpg','jpeg','gif'
    ];

    /**
     * 允许上传最大容量 (单位kb)
     * @var int
     */
    public $maxsize = 2048;

    /**
     * 待上传文件
     * @var \Illuminate\Support\Collection
     */
    protected $files;

    /**
     * 上传后的文件位置
     * @var \Illuminate\Support\Collection
     */
    protected $paths;

    /**
     * Upload constructor.
     * @param array $files          上传的文件
     * @param array $extensions     允许的后缀
     * @param int $maxsize          最大容量
     */
    public function __construct($files = [], array $extensions = [], $maxsize = 0)
    {
        if (! $files) {
            $files = request()->file();
        }
        $this->files = $files instanceof UploadedFile ? collect([$files]) : collect($files);
        $this->paths = collect([]);
        $this->maxsize($maxsize);
        $this->extension($extensions);
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
     * 允许上传最大容量
     * @param int $maxsize
     * @return $this
     */
    public function maxsize(int $maxsize = 0)
    {
        $this->maxsize = $maxsize ?: $this->maxsize;
        return $this;
    }

    /**
     * 上传
     * @param $path
     * @param array $options
     * @return false|string
     */
    public function store($path, $options = []) :Collection
    {
        $this->isValid();

        $this->check();

        $dir = Carbon::now()->format('Ymd');
        $this->files->map(function($file, $key) use($path, $options, $dir) {
            if(is_array($file)) {
                $this->paths->offsetSet($key, collect());
                foreach($file as $oneFile) {
                    $this->paths[$key]->push($oneFile->store($path.'/'.$dir, $options));
                }
            } else {
                $this->paths->offsetSet($key, $file->store($path.'/'.$dir, $options));
            }
        });
        return $this->paths;
    }

    /**
     * 验证文件
     * @throws UploadException
     */
    protected function check()
    {

        $this->files->map(function($file){
            if(is_array($file)) {
                foreach($file as $oneFile) {
                    if (! in_array($oneFile->extension(), $this->extensions) ) {
                        throw new UploadException('文件类型不允许');
                    }

                    if ($oneFile->getSize() / 1024 > $this->maxsize) {
                        throw new UploadException('文件大小超出限额');
                    }
                }
            } else {
                if (! in_array($file->extension(), $this->extensions) )
                {
                    throw new UploadException('文件类型不允许');
                }

                if ($file->getSize() / 1024 > $this->maxsize) {
                    throw new UploadException('文件大小超出限额');
                }
            }

        });

    }

    public static function getInstance($file = [])
    {
        return new static($file);
    }

    public static function file($file = [])
    {
        return self::getInstance($file);
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
            if(is_array($file)) {
                foreach($file as $oneFile) {
                    if(! $oneFile->isValid() )
                    {
                        throw new UploadException('文件在上传过程中发生了错误');
                    }
                }
            } else {
                if(! $file->isValid() )
                {
                    throw new UploadException('文件在上传过程中发生了错误');
                }
            }
        });
    }

}