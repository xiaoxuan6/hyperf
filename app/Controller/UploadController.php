<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use League\Flysystem\Filesystem;

/**
 * @AutoController()
 */
class UploadController extends AbstractController
{
    public function index()
    {
        return $this->outResponse(200, 'Hello Hyperf!');
    }

    /**
     * @Inject()
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Notes: 上传文件
     * Date: 2021/3/30 16:45
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function upload()
    {
        $file = $this->request->file("image");
        $filename = uniqid("hf_") . $file->getClientFilename();

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream("/public/" . $filename, $stream);
        fclose($stream);

        return "/public/" . $filename;
    }

    /**
     * Notes: 下载文件
     * Date: 2021/3/30 16:45
     * @param \Hyperf\HttpServer\Contract\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function download(\Hyperf\HttpServer\Contract\ResponseInterface $response)
    {
        return $response->download(BASE_PATH . "/runtime/public/hf_6062e44e8ded816cb8945d68943a1.jpg", "out.jpg");
    }

}
