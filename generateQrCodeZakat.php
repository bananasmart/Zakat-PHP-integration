<?php
use Carbon;
class ZakatInvoicing{

                    function zakat_getLength($value) {
                        return strlen($value);
                    }

                    function zakat_toHex($value) {
                        return pack("H*", sprintf("%02X", $value));
                    }

                    function zakat_toString($zakat_tag, $zakat_value, $zakat_length) {
                        $value = (string) $zakat_value;
                        return zakat_toHex($zakat_tag) . zakat_toHex($zakat_length) . $value;
                    }

                    function zakat_getTLV($dataToEncode) {
                        $zakat_TLVS = '';
                        for ($i = 0; $i < count($dataToEncode); $i++) {
                            $zakat_tag = $dataToEncode[$i][0];
                            $zakat_value = $dataToEncode[$i][1];
                            $zakat_length = zakat_getLength($zakat_value);
                            $zakat_TLVS .= zakat_toString($zakat_tag, $zakat_value, $zakat_length);
                        }
                        return $zakat_TLVS;
                    }
                    
                    $dataToEncode = [
                        [1, $dataFromDb->BARANCHNAME ],
                        [2, $dataFromDb->FEDID],
                        [3, Carbon::parse($dataFromDb->SALESDATE)],
                        [4, $dataFromDb->SUBTOTAL],
                        [5, $dataFromDb->TAX + $dataFromDb->TAX2]
                    ];

                    $zakat_TLV = zakat_getTLV($dataToEncode);
                    $zakat_QR = base64_encode($zakat_TLV);

                    
                    $PNG_TEMP_DIR = public_path() . '/libs/phpqrcode/temp/SalesInvoices';
                    include public_path() . '/libs/phpqrcode/qrlib.php';
                    if (!file_exists($PNG_TEMP_DIR))
                        mkdir($PNG_TEMP_DIR);
                    $qrcode = $zakat_QR;

                    $filename = $branchid . '_' . \Session::get('brandSelected') . '-' . md5($qrcode . '|' . 'L' . '|' . '4') . '.png';
                    $fullPath = $PNG_TEMP_DIR . '/' . $filename;
                    QRcode::png($qrcode, $fullPath, 'L', '4', 2);
                    $qrCodePath = "libs/phpqrcode/temp/SalesInvoices/" . $filename;
                    $data['qrCodePath'] = public_path() . '/' . $qrCodePath;
					
}