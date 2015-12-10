						<?php
/**
 * contacto@facturadigital.com.mx
 * Descripción: Clase para generar facturas electrónicas en formato CFDI en PHP vía Web Service SOAP
 */
require_once (  "lib/nusoap.php" );  

class FacturaDigital {


    /**
     * Realiza la llamada al método de timbrado del Web Service de FacturaDigital.com.mx
     *
     * @param string $usuario es el usuario registrado en FacturaDigital
     * @param string $password es el password registrado en FacturaDigital
     * @param string $layout es el texto que contiene los datos del comprobante a generar, basado en la plantilla.
     * @return array $cfdi Contiene el arreglo de cadenas de texto con la información del CFDI
     */
    public static function generarCFDI( $usuario, $password, $layout ){
        set_time_limit(0);

        try {

            $urlFacturaDigital = "https://www.facturadigital.com.mx/sistemacfdi32/webservices/TimbradoWS.php?wsdl";

            $client = new SoapClient( $urlFacturaDigital ,
                array('cache_wsdl' => WSDL_CACHE_NONE,'trace' => TRUE));

            // envia los parámetros al método de timbrado
            $cfdi = $client->generarCFDIPorTexto( $usuario , $password , $layout );  // llama al método de generación de CFDI

            // retornamos los datos del timbre (aquí viene la URL del XML y PDF para que los descargues a tu servidor)
            return $cfdi;

        } catch (Exception $e) {
            throw new Exception("Error al Timbrar: " . $e->getMessage() , 100);
            return false;
        }
    }

    /**
     * Llama al método de cancelacion de folios UUID  (** CANCELACIONES ILIMITADAS SIN COSTO **)
     *
     * @param string $usuario es el usuario proporcionado por el integrador, único por RFC
     * @param string $password es el password proporcionado por el integrador
     * @param string $uuid es el folio fiscal UUID del CFDI proporcionado por el SAT
     * @return boolean retorna TRUE si todo está correcto
     */
    public static function cancelarCFDI( $usuario, $password, $uuid ){
        set_time_limit(0);

        try {
            $urlFacturaDigital = "https://www.facturadigital.com.mx/sistemacfdi32/webservices/TimbradoWS.php?wsdl";

            $client = new SoapClient( $urlFacturaDigital ,
                array('cache_wsdl' => WSDL_CACHE_NONE,'trace' => TRUE));

            $cancelacion = $client->cancelarCFDI( $usuario , $password , $uuid ); // cancelamos

            // obtenemos la respuesta del web service
            return $cancelacion;

        } catch (Exception $e) {
            throw new Exception("Error al cancelar: " . $e->getMessage() , 200);
            return false;
        }
    }

}


// preparamos la plantilla que contiene los datos de la factura o recibo
$layout = "FA|850|2015-12-10T06:12:00|Monterrey, N.L.|ingreso|Pago en una sola Exhibición|Tarjeta de Credito|0009 Banamex|100.00|0.00|116.00|MXN|1|OC123|2015-12-10|Texto Adicional
Calzada del Valle (Sucursal)|90|int-10|Col. Del Valle||San Pedro Garza Garcia.|Nuevo León|México|76888
CME110320N47|CASA DEL MEZCAL S.A. DE C.V.
Av. Palmar|1043|Piso 4|Del Valle||San Pedro Garza Garcia|Nuevo Leon|México|62268
CONCEPTOS|2
7899701|Pieza|Mezcal Reposado premium|1.00|50.00|50.00
7843701|Pieza|Mezcal Reposado edicion limitada|1.00|50.00|50.00
IMPUESTOS_TRASLADADOS|1
IVA|16.00|16.00
IMPUESTOS_RETENIDOS|0
ISR|0|0";


// ahora, ejecutemos la prueba de timbrado
// utiliza las credenciales de demostración:
$cfdi_timbrado = FacturaDigital::generarCFDI("demo2014", "demo", $layout);

echo "<pre>";
var_dump($cfdi_timbrado);
echo "</pre>";

// recuerda que para obtener las variables de $cfdi_timbrado, deberás consultarlas de la siguiente forma:
// echo $cfdi_timbrado->UUID;

// ejemplo de cancelación
// FacturaDigital::cancelarCFDI( "demo2014", "demo", "17bd3680-5066-11e4-916c-0800200c9a66" );
?>