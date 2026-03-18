<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DNC Record Details | Tulong Kabataan</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
  <link rel="icon" href="{{ asset('img/log2.png') }}" type="image/png">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <style>
  /* Global */
  body {
    font-family: 'Open Sans', Arial, sans-serif;
    background: #f9fafb;
    margin: 0;
    padding: 0;
    color: #111827;
    line-height: 1.6;
    font-size: 16px;
  }

  /* PDF header */
  .pdf-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #2563eb; /* 🔵 Blue line */
  }
  .pdf-header-left h1 {
    font-size: 22px;
    font-weight: 800;
    color: #1e3a8a;
    margin: 0;
  }
  .pdf-header-left p {
    margin: 2px 0 0 0;
    font-size: 15px;
    color: #374151;
  }
  .pdf-header img {
    height: 55px;
  }

  /* Container (form only) */
  .container {
    max-width: 900px;
    margin: 15px auto;
    background: #fff;
    border-radius: 12px;
    padding: 25px 40px;
    box-shadow: 0 6px 18px rgba(0,0,0,.1);
  }

  /* Title and subtitle */
  .event-title {
    font-size: 26px;
    font-weight: 700;
    color: #1e3a8a;
    text-align: center;
    margin: 10px 0 6px 0;
  }
  .event-location {
    text-align: center;
    font-size: 15px;
    color: #374151;
    margin-bottom: 8px;
  }
  .priority {
    text-align: center;
    margin-bottom: 16px;
  }

  /* Priority pills */
  .pill {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
  }
  .red-pill { background:#fee2e2; color:#b91c1c; }
  .orange-pill { background:#fff7ed; color:#ea580c; }
  .green-pill { background:#ecfdf5; color:#065f46; }

  /* Section headers */
  h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1e3a8a;
    background:#f9fafb;
    padding: 8px 12px;
    border-left: 5px solid #2563eb;
    margin: 25px 0 12px;
    page-break-after: avoid;
  }

  /* Sections wrapper */
  .section {
    page-break-inside: avoid;
    margin-bottom: 20px;
  }

  /* Rows */
  .row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
    font-size: 15px;
    page-break-inside: avoid;
  }
  .label { font-weight:600; color:#374151; flex:1; }
  .value { flex:2; text-align:right; color:#111827; }

  /* Buttons */
  .back-btn {
    display:inline-flex;
    align-items:center;
    gap:6px;
    margin-top:20px;
    padding:10px 18px;
    border:1px solid #d1d5db;
    border-radius:8px;
    background:#fff;
    text-decoration:none;
    color:#2563eb;
    font-weight:600;
    transition:.2s;
    font-size:15px;
  }
  .back-btn:hover { background:#f3f4f6; }

  /* Footer */
  .footer {
    margin-top: 40px;
    padding: 20px;
    text-align: center;
    font-size: 14px;
    color:#f9fafb;
    background: linear-gradient(90deg, #1e3a8a, #2563eb);
  }
  .footer strong { color:#fff; }

  /* Print/PDF adjustments */
  @media print {
    body { background: white; font-size: 13pt; }
    .container {
      margin-top: 0 !important;
      padding-top: 15px !important;
      box-shadow: none;
      border: none;
    }
    .event-title {
      margin-top: 0 !important;
      padding-top: 0 !important;
    }
    .back-btn, .footer { display: none !important; }
    .page-break { page-break-before: always; }
    .page-break h2 { margin-top: 40px !important; }
    /* 🔵 Repeat header every page */
    .pdf-header {
      position: running(header);
    }
    @page {
      @top-center {
        content: element(header);
      }
    }
  }
  </style>
</head>
<body>

  <!-- Main Content -->
  <div class="container">

    <!-- New Header (inside form/PDF) -->
    <div class="pdf-header" id="header">
      <div class="pdf-header-left">
        <h1>Tulong Kabataan Network</h1>
        <p>Damage, Needs, and Capacities (DNC) Assessment Report</p>
      </div>
      <img src="{{ asset('img/log.png') }}" alt="Tulong Kabataan Logo">
    </div>

    <!-- Event title & details -->
    <h1 class="event-title">{{ $record->event ?? 'NONE' }}</h1>
    <p class="event-location">
      Location: {{ $record->barangay ?? 'NONE' }}, {{ $record->municipality ?? 'NONE' }}, {{ $record->province ?? 'NONE' }}
    </p>
    <div class="priority">
      <strong>Priority Level: </strong>
      <span class="pill 
        @if($record->priority == 'High') red-pill 
        @elseif($record->priority == 'Medium') orange-pill 
        @else green-pill @endif">
        {{ $record->priority ?? 'NONE' }}
      </span>
    </div>

    <!-- A. General -->
    <div class="section">
      <h2>A. General Information</h2>
      <div class="row"><span class="label">Date of Assessment:</span><span class="value">{{ $record->date ? $record->date->format('F d, Y') : 'NONE' }}</span></div>
      <div class="row"><span class="label">Assessor:</span><span class="value">{{ $record->assessor ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Affected Households:</span><span class="value">{{ $record->households ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Affected Individuals:</span><span class="value">{{ $record->individuals ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Population Breakdown:</span>
        <span class="value">
          Male: {{ $record->pop_male ?? '0' }} | Female: {{ $record->pop_female ?? '0' }} | 
          Children: {{ $record->pop_children ?? '0' }} | Elderly: {{ $record->pop_elderly ?? '0' }} | 
          PWDs: {{ $record->pop_pwds ?? '0' }}
        </span>
      </div>
    </div>

    <!-- B. Damage -->
    <div class="section">
      <h2>B. Damage Assessment</h2>
      <div class="row"><span class="label">Houses Fully Damaged:</span><span class="value">{{ $record->houses_full ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Houses Partially Damaged:</span><span class="value">{{ $record->houses_partial ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Infrastructure Damage:</span><span class="value">{{ $record->infrastructure ? implode(', ', $record->infrastructure) : 'NONE' }}</span></div>
      <div class="row"><span class="label">Crop Losses:</span><span class="value">{{ $record->crop_type ?? 'NONE' }} ({{ $record->crop_hectares ?? '0' }} hectares)</span></div>
      <div class="row"><span class="label">Livestock Lost:</span><span class="value">{{ $record->livestock_type ?? 'NONE' }} ({{ $record->livestock_number ?? '0' }} heads)</span></div>
      <div class="row"><span class="label">Tools Destroyed:</span><span class="value">{{ $record->tools_destroyed ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Community Facilities:</span><span class="value">{{ $record->facilities_affected ?? 'NONE' }} - {{ $record->facilities_notes ?? 'NONE' }}</span></div>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- C. Needs -->
    <div class="section">
      <h2>C. Needs</h2>
      <div class="row"><span class="label">Needs:</span><span class="value">{{ $record->needs ? implode(', ', $record->needs) : 'NONE' }}</span></div>
      <div class="row"><span class="label">Other Needs:</span><span class="value">{{ $record->needs_other ?? 'NONE' }}</span></div>
    </div>

    <!-- D. Capacities -->
    <div class="section">
      <h2>D. Capacities</h2>
      <div class="row"><span class="label">Groups:</span><span class="value">{{ $record->groups ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Facilities:</span><span class="value">{{ $record->facilities ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Skills:</span><span class="value">{{ $record->skills ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Initiatives:</span><span class="value">{{ $record->initiatives ?? 'NONE' }}</span></div>
    </div>

    <!-- E. Prioritization -->
    <div class="section">
      <h2>E. Prioritization</h2>
      <div class="row"><span class="label">Solutions:</span><span class="value">{{ $record->solutions ?? 'NONE' }}</span></div>
      <div class="row"><span class="label">Top Needs:</span>
        <span class="value">{{ $record->top_need_1 ?? 'NONE' }}, {{ $record->top_need_2 ?? 'NONE' }}, {{ $record->top_need_3 ?? 'NONE' }}</span>
      </div>
    </div>

  </div> <!-- END container -->

  <!-- Buttons -->
  <div style="text-align:center; margin-top:20px;">
    <a href="javascript:void(0)" onclick="downloadPDF()" class="back-btn">
      <i class="ri-file-download-line"></i> Download PDF
    </a>
    <a href="{{ route('dnc.view') }}" class="back-btn"><i class="ri-arrow-left-line"></i> Back to Records</a>
  </div>

  <!-- Footer -->
  <div class="footer">
    <p><strong>Tulong Kabataan Network</strong> • Generated on {{ now()->format('F d, Y h:i A') }}</p>
    <p>This document is for official use in humanitarian response and disaster needs assessment.</p>
  </div>

 <script>
function downloadPDF() {
  const element = document.querySelector('.container');


  const breaks = element.querySelectorAll('.page-break');
  breaks.forEach(breakEl => {
    const headerClone = document.querySelector('.pdf-header').cloneNode(true);
    headerClone.style.pageBreakBefore = "always"; 
    headerClone.style.marginBottom = "20px";    
    breakEl.parentNode.insertBefore(headerClone, breakEl.nextSibling);
  });

  const opt = {
    margin:       0.3,
    filename:     'dnc_report.pdf',
    image:        { type: 'jpeg', quality: 0.98 },
    html2canvas:  { scale: 2, useCORS: true, scrollY: 0 },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
  };

  html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>
