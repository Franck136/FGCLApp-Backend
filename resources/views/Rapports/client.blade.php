<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport Client — {{ $client->raison_sociale }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a2e; background: #fff; }

        /* ── HEADER ── */
        .header {
            background: linear-gradient(135deg, #0F1E35, #1D6FA4);
            color: #fff;
            padding: 28px 36px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .header-logo { font-size: 22px; font-weight: 900; letter-spacing: 1px; }
        .header-sub  { font-size: 11px; opacity: 0.7; margin-top: 3px; }
        .header-right { text-align: right; font-size: 11px; opacity: 0.8; }
        .header-title {
            background: #F0A500;
            color: #0F1E35;
            font-size: 13px;
            font-weight: 700;
            padding: 4px 14px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 6px;
        }

        /* ── BODY ── */
        .content { padding: 28px 36px; }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1D6FA4;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1D6FA4;
            padding-bottom: 5px;
            margin: 22px 0 12px;
        }

        /* ── INFO GRID ── */
        .info-grid { display: table; width: 100%; border-collapse: collapse; }
        .info-row  { display: table-row; }
        .info-label {
            display: table-cell;
            width: 35%;
            color: #6B84AA;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 5px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #1a1a2e;
            font-size: 12px;
            padding: 5px 0;
            vertical-align: top;
        }

        /* ── STAT BOXES ── */
        .stat-row { display: table; width: 100%; border-collapse: separate; border-spacing: 10px; }
        .stat-box {
            display: table-cell;
            background: #f0f4f8;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            border: 1px solid #dde3ed;
        }
        .stat-value { font-size: 26px; font-weight: 900; color: #1D6FA4; }
        .stat-label { font-size: 10px; color: #6B84AA; margin-top: 3px; text-transform: uppercase; }

        /* ── TABLE ── */
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #0F1E35; color: #fff; }
        thead th { padding: 8px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; text-align: left; }
        tbody tr:nth-child(even) { background: #f5f7fa; }
        tbody tr:nth-child(odd)  { background: #fff; }
        tbody td { padding: 7px 10px; font-size: 11px; color: #2a2a2a; border-bottom: 1px solid #e8ecf2; }

        /* ── BADGES ── */
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; }
        .badge-actif    { background: #d1fae5; color: #065f46; }
        .badge-expire   { background: #fee2e2; color: #991b1b; }
        .badge-suspendu { background: #fef3c7; color: #92400e; }
        .badge-resilie  { background: #ede9fe; color: #5b21b6; }
        .badge-bon      { background: #d1fae5; color: #065f46; }
        .badge-degrade  { background: #fef3c7; color: #92400e; }
        .badge-hors_service { background: #fee2e2; color: #991b1b; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 36px;
            border-top: 1px solid #dde3ed;
            padding: 14px 36px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ba8b8;
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <div class="header">
        <div>
            <div class="header-logo">FGCL SARL</div>
            <div class="header-sub">Services Informatiques · Douala, Cameroun</div>
            <div class="header-title">Rapport Client</div>
        </div>
        <div class="header-right">
            <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
            <div style="margin-top:4px; font-size:10px;">Réf. client : #{{ $client->id }}</div>
        </div>
    </div>

    <div class="content">

        <!-- IDENTIFICATION -->
        <div class="section-title">Identification</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Raison sociale</div>
                <div class="info-value"><strong>{{ $client->raison_sociale }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Forme juridique</div>
                <div class="info-value">{{ $client->forme_juridique ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Contribuable</div>
                <div class="info-value">{{ $client->numero_contribuable ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Secteur d'activité</div>
                <div class="info-value">{{ $client->secteur_activite }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Adresse</div>
                <div class="info-value">{{ $client->adresse }}, {{ $client->ville }}{{ $client->region ? ', '.$client->region : '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $client->telephone }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $client->email ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut</div>
                <div class="info-value">
                    <span class="badge badge-{{ $client->statut }}">{{ strtoupper($client->statut) }}</span>
                </div>
            </div>
        </div>

        <!-- CONTACT PRINCIPAL -->
        <div class="section-title">Contact principal</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nom & Prénom</div>
                <div class="info-value"><strong>{{ $client->nom_responsable }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Poste</div>
                <div class="info-value">{{ $client->poste_responsable ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone</div>
                <div class="info-value">{{ $client->telephone_responsable ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $client->email_responsable ?? '—' }}</div>
            </div>
        </div>

        <!-- STATISTIQUES -->
        <div class="section-title">Statistiques</div>
        <table style="width:100%; border-collapse:separate; border-spacing:8px;">
            <tr>
                <td style="background:#f0f4f8; border-radius:8px; padding:12px; text-align:center; border:1px solid #dde3ed;">
                    <div style="font-size:24px; font-weight:900; color:#1D6FA4;">{{ $client->contrats->count() }}</div>
                    <div style="font-size:10px; color:#6B84AA; margin-top:3px; text-transform:uppercase;">Contrats</div>
                </td>
                <td style="background:#f0f4f8; border-radius:8px; padding:12px; text-align:center; border:1px solid #dde3ed;">
                    <div style="font-size:24px; font-weight:900; color:#22C55E;">{{ $client->contrats->where('statut','actif')->count() }}</div>
                    <div style="font-size:10px; color:#6B84AA; margin-top:3px; text-transform:uppercase;">Contrats actifs</div>
                </td>
                <td style="background:#f0f4f8; border-radius:8px; padding:12px; text-align:center; border:1px solid #dde3ed;">
                    <div style="font-size:24px; font-weight:900; color:#F59E0B;">{{ $client->interventions->count() }}</div>
                    <div style="font-size:10px; color:#6B84AA; margin-top:3px; text-transform:uppercase;">Interventions</div>
                </td>
                <td style="background:#f0f4f8; border-radius:8px; padding:12px; text-align:center; border:1px solid #dde3ed;">
                    <div style="font-size:24px; font-weight:900; color:#A855F7;">{{ $client->equipements->count() }}</div>
                    <div style="font-size:10px; color:#6B84AA; margin-top:3px; text-transform:uppercase;">Équipements</div>
                </td>
            </tr>
        </table>

        <!-- CONTRATS -->
        @if($client->contrats->count() > 0)
        <div class="section-title">Contrats</div>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Date signature</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Durée</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->contrats as $contrat)
                <tr>
                    <td><strong>{{ $contrat->reference }}</strong></td>
                    <td>{{ str_replace('_', ' ', $contrat->type_contrat) }}</td>
                    <td>{{ \Carbon\Carbon::parse($contrat->date_signature)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}</td>
                    <td>{{ $contrat->duree_mois }} mois</td>
                    <td><span class="badge badge-{{ $contrat->statut }}">{{ strtoupper($contrat->statut) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- INTERVENTIONS -->
        @if($client->interventions->count() > 0)
        <div class="section-title">Interventions récentes</div>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Type</th>
                    <th>Date planifiée</th>
                    <th>Durée</th>
                    <th>Coût (FCFA)</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->interventions->take(10) as $intervention)
                <tr>
                    <td><strong>{{ $intervention->reference }}</strong></td>
                    <td>{{ $intervention->type_intervention }}</td>
                    <td>{{ \Carbon\Carbon::parse($intervention->date_planifiee)->format('d/m/Y') }}</td>
                    <td>{{ $intervention->duree_minutes ? floor($intervention->duree_minutes/60).'h'.($intervention->duree_minutes%60).'m' : '—' }}</td>
                    <td>{{ $intervention->cout ? number_format($intervention->cout, 0, ',', ' ') : '—' }}</td>
                    <td><span class="badge badge-{{ $intervention->statut == 'terminee' ? 'actif' : 'suspendu' }}">{{ strtoupper(str_replace('_', ' ', $intervention->statut)) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- ÉQUIPEMENTS -->
        @if($client->equipements->count() > 0)
        <div class="section-title">Parc informatique</div>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>N° Série</th>
                    <th>Localisation</th>
                    <th>État</th>
                </tr>
            </thead>
            <tbody>
                @foreach($client->equipements as $eq)
                <tr>
                    <td>{{ $eq->type_equipement }}</td>
                    <td>{{ $eq->marque }}</td>
                    <td>{{ $eq->modele }}</td>
                    <td>{{ $eq->numero_serie ?? '—' }}</td>
                    <td>{{ $eq->localisation ?? '—' }}</td>
                    <td><span class="badge badge-{{ $eq->etat }}">{{ strtoupper(str_replace('_', ' ', $eq->etat)) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div>FGCL SARL · Douala, Cameroun · Document confidentiel</div>
        <div>Généré le {{ now()->format('d/m/Y à H:i') }}</div>
    </div>

</body>
</html>
