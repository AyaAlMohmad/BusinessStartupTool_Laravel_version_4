@extends('layouts.app')

@section('content')
<style>
  .chart-container{position:relative;margin-bottom:20px}
  .card-title{font-size:1.1rem;font-weight:600;margin-bottom:1rem;text-align:center;color:#2c3e50}
  .stat-card{border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.08);border:none;margin-bottom:24px;transition:.3s}
  .stat-card:hover{transform:translateY(-5px);box-shadow:0 6px 16px rgba(0,0,0,.12)}
  .section-title{font-size:1.4rem;font-weight:600;margin:30px 0 20px;color:#34495e;border-bottom:2px solid #f1f1f1;padding-bottom:10px}
  #ageChart{height:360px!important}
  #migrationChart,#culturalChart,#migrationTabChart{height:300px!important}
  #languageChart,#timeChart{height:300px!important}
  #employmentChart,#educationChart{height:300px!important}
  #progressChart{height:420px!important}
  #bizBeforeChart{height:300px!important}
  #bizCompletedChart{height:320px!important}
  #personalOutChart,#empEduOutChart{height:280px!important}
  #bizOutChart{height:300px!important}
</style>

<div class="container-fluid py-4">
  <h4 class="section-title">Progress Analytics</h4>

  {{-- Tabs --}}
  <ul class="nav nav-tabs" id="analyticsTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-demographics" type="button">Demographic Overview</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-migration" type="button">Migration</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-progress" type="button">Progress</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-business" type="button">Business & Progress</button></li>
  </ul>

  <div class="tab-content mt-3" id="analyticsTabsContent">

    {{-- 1) Demographic Overview --}}
    <div class="tab-pane fade show active" id="tab-demographics" role="tabpanel">
      {{-- الصف 1: العداد + الأعمار --}}
      <div class="row">
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body text-center">
              <div class="card-title">Total number of people in this region</div>
              <div style="font-size:64px;font-weight:800;color:#f39c12;line-height:1">
                {{ (int)($totalParticipants ?? 0) }}
              </div>
              <div class="mt-2 text-muted">Participants</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Age Groups</div>
              <canvas id="ageChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- الصف 2: حالة/نوع الهجرة + الخلفية الثقافية --}}
      <div class="row">
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Migration status</div>
              <canvas id="migrationChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Cultural Background</div>
              <canvas id="culturalChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- الصف 3: اللغات + مدة الإقامة --}}
      <div class="row">
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Languages Spoken</div>
              <canvas id="languageChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Time in Australia</div>
              <canvas id="timeChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      {{-- الصف 4: التوظيف + التعليم --}}
      <div class="row">
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Employment Status</div>
              <canvas id="employmentChart"></canvas>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-6">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Education Level</div>
              <canvas id="educationChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 2) Migration (لوحة مستقلة) --}}
    <div class="tab-pane fade" id="tab-migration" role="tabpanel">
      <div class="row">
        <div class="col-12">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Migration Type / Visa Category</div>
              <canvas id="migrationTabChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 3) Progress (نِسَب إكمال الأقسام) --}}
    <div class="tab-pane fade" id="tab-progress" role="tabpanel">
      <div class="row">
        <div class="col-12">
          <div class="card stat-card">
            <div class="card-body chart-container">
              <div class="card-title">Completion Percentage by Section</div>
              <canvas id="progressChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 4) Business & Progress --}}
    <div class="tab-pane fade" id="tab-business" role="tabpanel">
      <div class="row">
        <div class="col-lg-6">
          <div class="card stat-card"><div class="card-body chart-container">
            <div class="card-title">Business stage before joining</div>
            <canvas id="bizBeforeChart"></canvas>
          </div></div>
        </div>
        <div class="col-lg-6">
          <div class="card stat-card"><div class="card-body chart-container">
            <div class="card-title">Business stage completed (cumulative %)</div>
            <canvas id="bizCompletedChart"></canvas>
          </div></div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-6">
          <div class="card stat-card"><div class="card-body chart-container">
            <div class="card-title">Personal Outcomes</div>
            <canvas id="personalOutChart"></canvas>
          </div></div>
        </div>
        <div class="col-lg-6">
          <div class="card stat-card"><div class="card-body chart-container">
            <div class="card-title">Employment/Education Outcomes</div>
            <canvas id="empEduOutChart"></canvas>
          </div></div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card stat-card"><div class="card-body chart-container">
            <div class="card-title">Business Outcomes</div>
            <canvas id="bizOutChart"></canvas>
          </div></div>
        </div>
      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const palette=['#3498db','#2ecc71','#e74c3c','#f39c12','#9b59b6','#1abc9c','#d35400','#34495e','#7f8c8d','#27ae60'];

function createBarChart(ctx,labels,data,options={}) {
  const base={responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}};
  return new Chart(ctx,{type:'bar',data:{labels,datasets:[{data,backgroundColor:labels.map((_,i)=>palette[i%palette.length]),borderColor:'#fff',borderWidth:1}]},options:{...base,...options}});
}
function createPieChart(ctx,labels,data){
  return new Chart(ctx,{type:'pie',data:{labels,datasets:[{data,backgroundColor:labels.map((_,i)=>palette[i%palette.length]),borderColor:'#fff',borderWidth:1}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{boxWidth:15,padding:15}}}}});
}
function createDoughnutChart(ctx,labels,data,cutout='60%'){
  return new Chart(ctx,{type:'doughnut',data:{labels,datasets:[{data,backgroundColor:labels.map((_,i)=>palette[i%palette.length]),borderColor:'#fff',borderWidth:1,hoverOffset:15}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'right',labels:{boxWidth:15,padding:15,font:{size:12}}},tooltip:{callbacks:{label:(c)=>`${c.label}: ${c.raw}%`}}},cutout}});
}
function createLineChart(ctx,labels,data){
  return new Chart(ctx,{type:'line',data:{labels,datasets:[{data,fill:false,tension:.3}]},
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,max:100}}}});
}

document.addEventListener('DOMContentLoaded',function(){
  // بيانات من السيرفر
const ageGroups     = @json($ageGroups ?? []);
const migrantStats  = @json($migrantStats ?? []);
const timeInCountry = @json($timeInCountry ?? []);
const progressData  = @json($sectionCompletion ?? []);
const businessStageBefore = @json($businessStageBefore ?? []);
const businessStageCompleted = @json($businessStageCompleted ?? []);
const personalOutcomes = @json($personalOutcomes ?? []);
const employmentEducationOutcomes = @json($employmentEducationOutcomes ?? []);
const businessOutcomes = @json($businessOutcomes ?? []);


  // --- Demographics ---
  createBarChart(
    document.getElementById('ageChart').getContext('2d'),
    ['Under 18','18-25','26-35','36-45','46-55','55+'],
    Object.values(ageGroups||{}),
    {indexAxis:'x'}
  );
  createBarChart(
    document.getElementById('migrationChart').getContext('2d'),
    Object.keys(migrantStats.visa_category||{}),
    Object.values(migrantStats.visa_category||{}),
    {indexAxis:'x'}
  );
  createPieChart(
    document.getElementById('culturalChart').getContext('2d'),
    Object.keys(migrantStats.cultural_background||{}),
    Object.values(migrantStats.cultural_background||{})
  );
  createPieChart(
    document.getElementById('languageChart').getContext('2d'),
    Object.keys(migrantStats.languages||{}),
    Object.values(migrantStats.languages||{})
  );
  createBarChart(
    document.getElementById('timeChart').getContext('2d'),
    Object.keys(timeInCountry||{}),
    Object.values(timeInCountry||{}),
    {indexAxis:'y'}
  );
  createBarChart(
    document.getElementById('employmentChart').getContext('2d'),
    Object.keys(migrantStats.employment_status||{}),
    Object.values(migrantStats.employment_status||{}),
    {indexAxis:'x'}
  );
  createBarChart(
    document.getElementById('educationChart').getContext('2d'),
    Object.keys(migrantStats.education_level||{}),
    Object.values(migrantStats.education_level||{}),
    {indexAxis:'y'}
  );

  // --- Migration tab (لوحة منفصلة) ---
  createBarChart(
    document.getElementById('migrationTabChart').getContext('2d'),
    Object.keys(migrantStats.visa_category||{}),
    Object.values(migrantStats.visa_category||{}),
    {indexAxis:'x'}
  );

  // --- Progress tab ---
  createDoughnutChart(
    document.getElementById('progressChart').getContext('2d'),
    Object.keys(progressData).map(l=>l.replace('_',' ')),
    Object.values(progressData)
  );

  // --- Business & Progress tab ---
  // قبل الانضمام
  createBarChart(
    document.getElementById('bizBeforeChart').getContext('2d'),
    Object.keys(businessStageBefore),
    Object.values(businessStageBefore)
);

// تقدم مكتمل (نِسَب تراكمية)
createLineChart(
    document.getElementById('bizCompletedChart').getContext('2d'),
    businessStageCompleted.labels || [],
    businessStageCompleted.percent || []
);

// Personal Outcomes
createBarChart(
    document.getElementById('personalOutChart').getContext('2d'),
    Object.keys(personalOutcomes),
    Object.values(personalOutcomes),
    {indexAxis: 'y'}
);

// Employment/Education Outcomes
createBarChart(
    document.getElementById('empEduOutChart').getContext('2d'),
    Object.keys(employmentEducationOutcomes),
    Object.values(employmentEducationOutcomes)
);

// Business Outcomes
createBarChart(
    document.getElementById('bizOutChart').getContext('2d'),
    Object.keys(businessOutcomes),
    Object.values(businessOutcomes)
);

  // إعادة ضبط المقاسات عند تبديل التبويبات
  const tabsEl=document.getElementById('analyticsTabs');
  if(tabsEl){
    tabsEl.addEventListener('shown.bs.tab',()=>Chart.helpers.each(Chart.instances,(inst)=>{try{inst.resize();}catch(e){}}));
  }
});
</script>
@endsection
