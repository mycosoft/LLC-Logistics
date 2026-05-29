<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Http\Requests\ImportClientsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientImportController extends Controller
{
    /**
     * Format phone number to 256 international format for CSV import.
     */
    private static function formatPhoneForImport($value): string
    {
        if (!empty($value)) {
            $value = preg_replace('/[^0-9+]/', '', $value);
            $value = ltrim($value, '+');
            if (str_starts_with($value, '0')) {
                $value = '256' . substr($value, 1);
            } elseif (!str_starts_with($value, '256')) {
                $value = '256' . $value;
            }
        }
        return $value ?: '';
    }

    /**
     * Show the import form
     */
    public function showImportForm()
    {
        return view('clients.import');
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="clients_import_template.csv"',
        ];

        $columns = ['name', 'email', 'phone', 'company', 'address'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Write header row
            fputcsv($file, $columns);
            
            // Write sample data rows
            fputcsv($file, ['John Doe', 'john@example.com', '0700123456', 'ABC Company', '123 Main St, Kampala']);
            fputcsv($file, ['Jane Smith', 'jane@example.com', '0700654321', 'XYZ Ltd', '456 Oak Ave, Entebbe']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process CSV import
     */
    public function import(ImportClientsRequest $request)
    {
        $file = $request->file('csv_file');
        
        $imported = 0;
        $skipped = 0;
        $errors = [];
        $skippedRows = [];

        // Open and read CSV file
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // Read header row
            
            // Validate header
            $expectedHeaders = ['name', 'email', 'phone', 'company', 'address'];
            if ($header !== $expectedHeaders) {
                return redirect()->route('clients.import')
                    ->with('error', 'Invalid CSV format. Please use the provided template. Expected columns: ' . implode(', ', $expectedHeaders));
            }

            $rowNumber = 1; // Start from 1 (after header)
            
            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    continue;
                }

                // Prepare client data
                $clientData = [
                    'name' => $data[0] ?? '',
                    'email' => $data[1] ?? '',
                    'phone' => $data[2] ?? '',
                    'company' => $data[3] ?? null,
                    'address' => $data[4] ?? null,
                ];

                // Format phone number to 256 format
                $clientData['phone'] = self::formatPhoneForImport($clientData['phone']);

                // Validate row data
                $validator = Validator::make($clientData, [
                    'name' => 'required|string|max:255',
                    'email' => 'nullable|email|unique:clients,email',
                    'phone' => 'required|string|max:255',
                    'company' => 'nullable|string|max:255',
                    'address' => 'nullable|string',
                ]);

                if ($validator->fails()) {
                    $skipped++;
                    $errorMessages = $validator->errors()->all();
                    $errors[] = "Row {$rowNumber}: " . implode(', ', $errorMessages);
                    $skippedRows[] = [
                        'row' => $rowNumber,
                        'data' => $clientData,
                        'errors' => $errorMessages,
                    ];
                    continue;
                }

                // Create client
                try {
                    Client::create($clientData);
                    $imported++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Row {$rowNumber}: Failed to create client - " . $e->getMessage();
                    $skippedRows[] = [
                        'row' => $rowNumber,
                        'data' => $clientData,
                        'errors' => [$e->getMessage()],
                    ];
                }
            }

            fclose($handle);
        }

        // Prepare result message
        $message = "Import completed: {$imported} clients imported";
        if ($skipped > 0) {
            $message .= ", {$skipped} rows skipped";
        }

        return view('clients.import-results', compact('imported', 'skipped', 'errors', 'skippedRows'))
            ->with('success', $message);
    }
}
