<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Paiement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px 0;
        }
        .payment-details, .billing-info {
            background-color: #f8f9fa;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .payment-details h2, .billing-info h2 {
            color: #2c3e50;
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: normal;
            color: #666;
        }
        .detail-value {
            font-weight: bold;
            text-align: right;
        }
        .total {
            font-size: 18px;
            color: #2c3e50;
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100%;
                padding: 10px;
            }
            .payment-details, .billing-info {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmation de Paiement</h1>
            <p>Bonjour {{ $name ?? 'Client' }},</p>
            <p>Merci pour votre achat sur notre plateforme d'apprentissage en ligne!</p>
        </div>
        
        <div class="content">
            <div class="payment-details">
                <h2>Détails du paiement</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Montant</span>
                    <span class="detail-value">{{ $amount ?? '123,00 €' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Méthode de paiement</span>
                    <span class="detail-value">{{ $payment_method ?? 'Carte bancaire' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Frais de service</span>
                    <span class="detail-value">{{ $service_fee ?? '0,00 €' }}</span>
                </div>
                
                <div class="detail-row total">
                    <span class="detail-label">Total</span>
                    <span class="detail-value">{{ $total ?? '123,00 €' }}</span>
                </div>
            </div>
            
            <div class="billing-info">
                <h2>Informations de facturation</h2>
                
                <div class="detail-row">
                    <span class="detail-label">Nom</span>
                    <span class="detail-value">{{ $name ?? 'Client' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Adresse</span>
                    <span class="detail-value">{{ $address ?? '' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Code postal</span>
                    <span class="detail-value">{{ $postal_code ?? '' }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Pays</span>
                    <span class="detail-value">{{ $country ?? '' }}</span>
                </div>
            </div>
            
            <p>{{ $name ?? 'Client' }}, si vous avez des questions concernant votre paiement, n'hésitez pas à nous contacter. Nous vous remercions de votre confiance.</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} E-Learning Platform. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>