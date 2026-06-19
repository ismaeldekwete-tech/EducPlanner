<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Emploi du Temps') }} - IUC</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.3;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        /* Header styling */
        .header-container {
            width: 100%;
            margin-bottom: 8px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
            border: none;
            padding: 0;
        }
        .header-left {
            text-align: center;
            width: 38%;
            font-size: 9px;
            font-weight: 500;
        }
        .header-right {
            text-align: center;
            width: 38%;
            font-size: 9px;
            font-weight: 500;
        }
        .header-logo {
            text-align: center;
            width: 24%;
            vertical-align: middle;
        }
        .national-title {
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .national-sub {
            font-style: italic;
            font-size: 8px;
            color: #64748b;
            margin-bottom: 2px;
        }
        .iuc-brand {
            color: #1e3a8a;
            font-weight: bold;
            font-size: 11px;
            margin-top: 3px;
        }
        .divider-line {
            border-top: 2px double #1e3a8a;
            margin: 4px 0 10px 0;
            width: 100%;
        }

        /* Main Title Banner */
        .title-banner {
            text-align: center;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 12px;
        }
        .title-main {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .title-sub {
            font-size: 10px;
            color: #475569;
            margin: 0;
            font-weight: 600;
        }

        /* Timetable Grid */
        .timetable-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 15px;
            background-color: #ffffff;
        }
        .timetable-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            border: 1px solid #1e3a8a;
            padding: 6px 4px;
            text-align: center;
        }
        .timetable-table td {
            border: 1px solid #cbd5e1;
            padding: 4px;
            height: 62px;
            vertical-align: middle;
            text-align: center;
        }
        .day-cell {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e293b;
            font-size: 10px;
            text-transform: uppercase;
            width: 80px;
            border-right: 2px solid #cbd5e1 !important;
        }

        /* Entry Cards */
        .entry-card {
            border: 1px solid #e2e8f0;
            border-left-width: 4px;
            border-radius: 4px;
            padding: 4px 6px;
            text-align: left;
            background-color: #f8fafc;
            min-height: 52px;
        }
        .entry-cm {
            border-left-color: #2563eb; /* Blue */
            background-color: #eff6ff;
        }
        .entry-td {
            border-left-color: #059669; /* Green */
            background-color: #ecfdf5;
        }
        .entry-tp {
            border-left-color: #dc2626; /* Red */
            background-color: #fef2f2;
        }

        .entry-badge {
            display: inline-block;
            font-size: 7px;
            font-weight: bold;
            padding: 1px 3px;
            border-radius: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .badge-cm { background-color: #dbeafe; color: #1e40af; }
        .badge-td { background-color: #d1fae5; color: #065f46; }
        .badge-tp { background-color: #fee2e2; color: #991b1b; }

        .entry-subject {
            font-weight: bold;
            font-size: 8.5px;
            color: #0f172a;
            line-height: 1.1;
            margin-bottom: 2px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .entry-detail {
            font-size: 8px;
            color: #475569;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Footer styling */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .footer-table td {
            border: none;
            vertical-align: top;
            padding: 0;
        }
        .footer-left {
            width: 50%;
            font-size: 8.5px;
            color: #64748b;
        }
        .footer-right {
            width: 50%;
            text-align: right;
            font-size: 9.5px;
            font-weight: bold;
            color: #1e293b;
        }
        .signature-space {
            margin-top: 25px;
            height: 50px;
            border-bottom: 1px dashed #cbd5e1;
            width: 180px;
            float: right;
        }

        /* Watermark watermark-text */
        .watermark {
            position: absolute;
            top: 45%;
            left: 20%;
            width: 60%;
            text-align: center;
            opacity: 0.04;
            font-size: 60px;
            font-weight: bold;
            color: #1e3a8a;
            transform: rotate(-25deg);
            z-index: -1000;
        }
    </style>
</head>
<body>

    <div class="watermark">IUC - 3IAC</div>

    <!-- Cameroun / IUC official header -->
    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    <div class="national-title">République du Cameroun</div>
                    <div class="national-sub">Paix - Travail - Patrie</div>
                    <div>Ministère de l'Enseignement Supérieur</div>
                    <div class="iuc-brand">Institut Universitaire de la Côte</div>
                    <div style="font-size: 7px; color: #475569;">Pôle d'Excellence Technologique de l'Afrique Centrale</div>
                </td>
                <td class="header-logo">
                    <!-- High-resolution application logo for PDF exports -->
                    <img src="{{ public_path('logo.png') }}" style="height: 50px; width: auto; max-width: 80px; display: block; margin: 0 auto;" alt="EducPlanner Logo">
                </td>
                <td class="header-right">
                    <div class="national-title">Republic of Cameroon</div>
                    <div class="national-sub">Peace - Work - Fatherland</div>
                    <div>Ministry of Higher Education</div>
                    <div class="iuc-brand">University Institute of the Coast</div>
                    <div style="font-size: 7px; color: #475569;">Central Africa's Technological Center of Excellence</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider-line"></div>

    <!-- Banner containing details -->
    <div class="title-banner">
        @if($type === 'teacher')
            <h1 class="title-main">EMPLOI DU TEMPS PERSONNEL DE L'ENSEIGNANT / TEACHER PERSONAL TIMETABLE</h1>
            <p class="title-sub">
                {{ __('Enseignant / Teacher :') }} {{ $teacher->name }}
                &nbsp;|&nbsp;
                {{ __('Département / Department :') }} {{ $teacher->department->name ?? '3IAC' }}
                &nbsp;|&nbsp;
                {{ __('Semaine / Week :') }} {{ $week }} ({{ $academicYear }})
            </p>
        @else
            <h1 class="title-main">EMPLOI DU TEMPS HEBDOMADAIRE / WEEKLY TIMETABLE</h1>
            <p class="title-sub">
                {{ __('Classe / Class :') }} {{ $classe->code_unique }}
                &nbsp;|&nbsp;
                {{ __('Filière / Option :') }} {{ $classe->filiere }}
                &nbsp;|&nbsp;
                {{ __('Régime / Regime :') }} {{ $classe->regime === 'J' ? __('Jour / Day') : __('Soir / Evening') }}
                &nbsp;|&nbsp;
                {{ __('Semaine / Week :') }} {{ $week }} ({{ $academicYear }})
            </p>
        @endif
    </div>

    <!-- Grid Table -->
    <table class="timetable-table">
        <thead>
            <tr>
                <th style="width: 80px;">{{ __('Jours / Days') }}</th>
                @foreach($slots as $slot)
                    <th>{{ $slot['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $day)
                <tr>
                    <td class="day-cell">{{ __($day) }}</td>
                    @foreach($slots as $slot)
                        @php
                            $entry = $entries->first(function ($e) use ($day, $slot) {
                                return strtolower($e->day_of_week) === strtolower($day) && $e->slot_number === $slot['label'];
                            });
                        @endphp
                        <td>
                            @if($entry)
                                @php
                                    $eType = $entry->subjectTeacher?->type ?? 'CM';
                                    $subClass = $eType === 'CM' ? 'entry-cm' : ($eType === 'TD' ? 'entry-td' : 'entry-tp');
                                    $badgeClass = $eType === 'CM' ? 'badge-cm' : ($eType === 'TD' ? 'badge-td' : 'badge-tp');

                                    $subjectName = $entry->subjectTeacher?->subject?->name ?? 'N/A';
                                    $roomName = $entry->room?->name ?? 'N/A';

                                    if ($type === 'teacher') {
                                        $detailText = __('Classe : ') . ($entry->timetable?->classe?->code_unique ?? 'N/A');
                                    } else {
                                        $detailText = __('Prof : ') . ($entry->subjectTeacher?->teacher?->name ?? 'N/A');
                                    }
                                @endphp
                                <div class="entry-card {{ $subClass }}">
                                    <span class="entry-badge {{ $badgeClass }}">{{ $eType }}</span>
                                    <div class="entry-subject">{{ $subjectName }}</div>
                                    <div class="entry-detail">{{ $detailText }}</div>
                                    <div class="entry-detail">{{ __('Salle : ') }}{{ $roomName }}</div>
                                </div>
                            @else
                                <span style="color: #cbd5e1; font-weight: 300;">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer of the printed timetable -->
    <table class="footer-table">
        <tr>
            <td class="footer-left">
                <div>* <strong>CM</strong> : {{ __('Cours Magistral (Magisterial Course)') }} | <strong>TD</strong> : {{ __('Travaux Dirigés (Directed Works)') }} | <strong>TP</strong> : {{ __('Travaux Pratiques (Practical Works)') }}</div>
                <div style="margin-top: 5px;">{{ __('Document généré automatiquement par EducPlanner le') }} {{ date('d/m/Y H:i:s') }}</div>
                <div>{{ __('L\'authenticité de ce document peut être vérifiée auprès de l\'administration de l\'IUC.') }}</div>
            </td>
            <td class="footer-right">
                <div>{{ __('Visa de la Direction / Signature of Director') }}</div>
                <div style="font-size: 8px; font-weight: normal; color: #64748b; margin-top: 2px;">{{ __('Le Directeur / The Director') }}</div>
                <div class="signature-space"></div>
            </td>
        </tr>
    </table>

</body>
</html>
