<?php

namespace App\Jobs;

use App\Models\UserData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class ProcessUserRiskAnalysis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private UserData $userData)
    {
    }

    public function handle(): void
    {
        try {
            $riskLevel = $this->determineRiskLevel();
            $this->userData->update(['risk_level' => $riskLevel]);
            
            $pdfPath = $this->generatePdfReport($riskLevel);
            
            Log::info("Risk analysis completed", [
                'cpf' => $this->userData->cpf,
                'risk_level' => $riskLevel,
                'pdf_path' => $pdfPath
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to process risk analysis", [
                'cpf' => $this->userData->cpf,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->release(30);
        }
    }

    private function determineRiskLevel(): string
    {
        $isHighRiskArea = in_array(
            $this->userData->address_data['localidade'] ?? null,
            ['São Paulo', 'Rio de Janeiro']
        );
        
        if ($this->userData->cpf_status === 'negativado' && $isHighRiskArea) {
            return 'high';
        }
        
        if ($this->userData->cpf_status === 'negativado') {
            return 'medium';
        }
        
        return 'low';
    }

    private function generatePdfReport(string $riskLevel): string
    {
        $directory = storage_path('app/reports');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $pdf = Pdf::loadView('reports.user_risk', [
            'userData' => $this->userData,
            'riskLevel' => $riskLevel,
        ]);
        
        $filename = "report_{$this->userData->cpf}.pdf";
        $fullPath = "{$directory}/{$filename}";
        $pdf->save($fullPath);
        
        if (!File::exists($fullPath)) {
            throw new \RuntimeException("Failed to save PDF file at {$fullPath}");
        }
        
        return $fullPath;
    }
}