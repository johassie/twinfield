<?php

namespace PhpTwinfield\ApiConnectors;

use PhpTwinfield\Exception;
use PhpTwinfield\Office;
use PhpTwinfield\Request as Request;
use PhpTwinfield\Supplier;
use PhpTwinfield\DomDocuments\SuppliersDocument;
use PhpTwinfield\Mappers\SupplierMapper;

/**
 * A facade to make interaction with the the Twinfield service easier when trying to retrieve or send information about
 * Suppliers.
 *
 * If you require more complex interactions or a heavier amount of control over the requests to/from then look inside
 * the methods or see the advanced guide detailing the required usages.
 *
 * @author Leon Rowland <leon@rowland.nl>
 * @copyright (c) 2013, Pronamic
 */
class SupplierApiConnector extends ProcessXmlApiConnector
{
    /**
     * Requests a specific supplier based off the passed in code and optionally the office.
     *
     * @param string $code
     * @param Office $office
     * @return Supplier The requested supplier
     * @throws Exception
     */
    public function get($code, Office $office): Supplier
    {
        // Make a request to read a single customer. Set the required values
        $request_customer = new Request\Read\Supplier();
        $request_customer
            ->setOffice($office->getCode())
            ->setCode($code);

        $response = $this->sendDocument($request_customer);

        return SupplierMapper::map($response);
    }

    /**
     * Requests all customers from the List Dimension Type.
     *
     * @param Office $office
     * @param string $dimType
     * @return array A multidimensional array in the following form:
     *               [$supplierId => ['name' => $name, 'shortName' => $shortName], ...]
     * @throws Exception
     */
    public function listAll(Office $office, string $dimType = 'CRD'): array
    {

        // Make a request to a list of all customers
        $request_customers = new Request\Catalog\Dimension($office->getCode(), $dimType);

        // Send the Request document and set the response to this instance.
        $response = $this->sendDocument($request_customers);

        // Get the raw response document
        $responseDOM = $response->getResponseDocument();

        // Prepared empty customer array
        $suppliers = [];

        // Store in an array by customer id
        foreach ($responseDOM->getElementsByTagName('dimension') as $supplier) {
            $supplier_id = $supplier->textContent;

            if (!is_numeric($supplier_id)) {
                continue;
            }

            $suppliers[$supplier->textContent] = array(
                'name' => $supplier->getAttribute('name'),
                'shortName' => $supplier->getAttribute('shortname'),
            );
        }

        return $suppliers;
    }

    /**
     * Sends a \PhpTwinfield\Supplier\Supplier instance to Twinfield to update or add.
     *
     * If you want to map the response back into a customer use getResponse()->getResponseDocument()->asXML() into the
     * SupplierMapper::map() method.
     *
     * @param Supplier $supplier
     * @throws Exception
     */
    public function send(Supplier $supplier): void
    {
        // Gets a new instance of SuppliersDocument and sets the $supplier
        $suppliersDocument = new SuppliersDocument();
        $suppliersDocument->addSupplier($supplier);

        $this->sendDocument($suppliersDocument);
    }
}
