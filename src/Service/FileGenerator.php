<?php
// src/Service/FileGenerator.php
namespace App\Service;

use Dompdf\Dompdf;

use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;

use setasign\FpdiProtection\FpdiProtection;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Class Tools
 * @package App\Service
 */
class FileGenerator
{
    /**
     * @param string $filename
     * @param string $content
     * @param string|null $owner_password
     * @param string|null $user_password
     * @return BinaryFileResponse
     */
    public function pdfGenerator(string $filename, string $content, ?string $user_password = null, ?string $owner_password = null): BinaryFileResponse
    {
        $dompdf = new DOMPDF();

        $dompdf->setPaper('a4');

        $dompdf->loadHtml($content);
        $dompdf->render();

        $filetemp = str_replace('.pdf','_temp.pdf',$filename);

        file_put_contents($filetemp, $dompdf->output());

        try
        {
            $filename = $this->pdfEncrypt($filetemp, $filename, $user_password, $owner_password);
        }
        catch (PdfParserException | PdfReaderException $e)
        {
            echo $e->getMessage();
        }

        unlink($filetemp);

        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response->deleteFileAfterSend();
    }

    /**
     * @param $origFile
     * @param $destFile
     * @param string|null $owner_password
     * @param string|null $user_password
     * @return mixed
     * @throws PdfParserException
     * @throws PdfReaderException
     */
    private function pdfEncrypt ($origFile, $destFile, ?string $user_password, ?string $owner_password): mixed
    {
        $pdf = new FpdiProtection();

        $pagecount = $pdf->setSourceFile($origFile);

        for ($loop = 1; $loop <= $pagecount; $loop++)
        {
            $tplidx = $pdf->importPage($loop);
            $pdf->addPage();
            $pdf->useTemplate($tplidx);
        }

        $pdf->SetProtection(array('print'), $user_password, $owner_password);
        $pdf->Output($destFile,'F');

        return $destFile;
    }
}
