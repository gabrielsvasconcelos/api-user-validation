<!DOCTYPE html>
<html>
<head>
    <title>User Risk Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .risk-high { color: red; font-weight: bold; }
        .risk-medium { color: orange; }
        .risk-low { color: green; }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Risk Analysis Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    
    <div class="section">
        <h2>User Information</h2>
        <p><strong>CPF:</strong> {{ $userData->cpf }}</p>
        <p><strong>Email:</strong> {{ $userData->email }}</p>
        <p><strong>CEP:</strong> {{ $userData->cep }}</p>
    </div>
    
    <div class="section">
        <h2>Address Information</h2>
        <p><strong>Street:</strong> {{ $userData->address_data['logradouro'] ?? 'N/A' }}</p>
        <p><strong>Neighborhood:</strong> {{ $userData->address_data['bairro'] ?? 'N/A' }}</p>
        <p><strong>City:</strong> {{ $userData->address_data['localidade'] ?? 'N/A' }}</p>
        <p><strong>State:</strong> {{ $userData->address_data['uf'] ?? 'N/A' }}</p>
    </div>
    
    <div class="section">
        <h2>Risk Analysis</h2>
        <p><strong>CPF Status:</strong> {{ ucfirst($userData->cpf_status) }}</p>
        <p><strong>Risk Level:</strong> 
            <span class="risk-{{ $riskLevel }}">{{ ucfirst($riskLevel) }} risk</span>
        </p>
    </div>
</body>
</html>