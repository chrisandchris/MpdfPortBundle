<?php
namespace TFox\MpdfPortBundle\Service;

use Symfony\Component\HttpFoundation\Response;

class MpdfService {

    /**
     * Get an instance of mPDF class
     * @param array $constructorArgs arguments for mPDF constructor
     * @return \mPDF
     */
    public function getMpdf($constructorArgs = array('utf-8')) {
        $reflection = new \ReflectionClass('\mPDF');
        /** @var \mPDF $mpdf */
        $mpdf = $reflection->newInstanceArgs($constructorArgs);
        $mpdf->allow_charset_conversion = false;
        $mpdf->debug = true;
        return $mpdf;
    }

    /**
     * @param \mPDF $mPDF
     * @param $html
     * @param string $filename
     * @param bool $debug
     * @return Response
     */
    public function getResponse(\mPDF $mPDF, $html, $filename = 'file', $debug = false) {
        $response = new Response();
        $response->setStatusCode(200);
        $response->headers->set('Pragma', 'no-cache', true);
        if ($debug OR $_GET['debug'] == true) {
            $debug = true;
            $response->headers->set('Content-Type', 'text/html', true);
        } else {
            $response->headers->set('Content-Type', 'application/pdf', true);
        }
        $response->headers->set('Content-Disposition', 'inline; filename=' . $filename . '.pdf', true);

        if ($debug) {
            $response->setContent($html);
        } else {
            $mPDF->WriteHTML($html);
            $response->setContent($mPDF->Output(null, 'S'));
        }
        return $response;
    }
}
