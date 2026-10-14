import http from 'k6/http';
import { check, sleep } from 'k6';
import { Counter, Rate, Trend } from 'k6/metrics';

// ============================================================================
// SAFE LOAD TESTING SCRIPT FOR WEBUZO SHARED HOSTING
// ============================================================================
// Usage instructions:
//   k6 run -e STAGE=smoke tests/Performance/safe-load-test.js
//   k6 run -e STAGE=low tests/Performance/safe-load-test.js
//   k6 run -e STAGE=moderate tests/Performance/safe-load-test.js
//   k6 run -e STAGE=controlled tests/Performance/safe-load-test.js
//   k6 run -e STAGE=higher -e ALLOW_HIGHER_LOAD=true tests/Performance/safe-load-test.js
//
// Target URL configuration:
//   k6 run -e BASE_URL=http://skandapras.test -e STAGE=smoke tests/Performance/safe-load-test.js
// ============================================================================

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1:8000';
const STAGE = __ENV.STAGE || 'smoke';
const ALLOW_PRODUCTION = __ENV.ALLOW_PRODUCTION === 'true';

// Safety Guard: Refuse to run against production by default
if (!ALLOW_PRODUCTION && (BASE_URL.includes('smkn2karanganyar.sch.id') || !BASE_URL.includes('localhost') && !BASE_URL.includes('127.0.0.1') && !BASE_URL.includes('.test'))) {
    throw new Error(
        `[SAFETY ABORT] Refusing to run load tests against unconfirmed host '${BASE_URL}'. ` +
        `Pass -e ALLOW_PRODUCTION=true only if you have authorization and maintenance window scheduled.`
    );
}

// Stage definitions tailored for shared hosting entry-process ceilings
const STAGE_CONFIGS = {
    smoke: {
        stages: [
            { duration: '10s', target: 5 },
            { duration: '20s', target: 5 },
            { duration: '5s', target: 0 },
        ],
    },
    low: {
        stages: [
            { duration: '15s', target: 10 },
            { duration: '45s', target: 10 },
            { duration: '10s', target: 0 },
        ],
    },
    moderate: {
        stages: [
            { duration: '20s', target: 20 },
            { duration: '40s', target: 20 },
            { duration: '10s', target: 0 },
        ],
    },
    controlled: {
        stages: [
            { duration: '20s', target: 30 },
            { duration: '40s', target: 30 },
            { duration: '15s', target: 0 },
        ],
    },
    higher: {
        stages: [
            { duration: '20s', target: 50 },
            { duration: '40s', target: 50 },
            { duration: '20s', target: 0 },
        ],
    },
    recovery: {
        stages: [
            { duration: '10s', target: 20 },
            { duration: '20s', target: 2 },
            { duration: '30s', target: 2 },
        ],
    },
};

const selectedStage = STAGE_CONFIGS[STAGE] || STAGE_CONFIGS.smoke;

if (STAGE === 'higher' && __ENV.ALLOW_HIGHER_LOAD !== 'true') {
    throw new Error(
        '[SAFETY ABORT] High load stage (50 VUs) on shared hosting requires explicit review and -e ALLOW_HIGHER_LOAD=true.'
    );
}

export const options = {
    stages: selectedStage.stages,
    thresholds: {
        // Halt/Fail immediately if HTTP error rate exceeds 2%
        http_req_failed: ['rate<0.02'],
        // 95th percentile response latency should stay under 2000ms
        http_req_duration: ['p(95)<2000'],
    },
    // Avoid unbounded request spam
    discardResponseBodies: false,
};

// Custom metrics
const errorCount = new Counter('custom_errors');
const homeDuration = new Trend('duration_homepage');
const sarprasDuration = new Trend('duration_sarpras');
const mapDuration = new Trend('duration_sarpras_map');
const newsDuration = new Trend('duration_news');

export default function () {
    const params = {
        headers: {
            'User-Agent': 'k6-Webuzo-Audit/1.0',
            'Accept': 'text/html,application/xhtml+xml',
            'Accept-Encoding': 'gzip, deflate',
        },
        timeout: '10s',
    };

    // 1. Homepage (GET /)
    const homeRes = http.get(`${BASE_URL}/`, params);
    homeDuration.add(homeRes.timings.duration);
    const homeSuccess = check(homeRes, {
        'homepage status is 200': (r) => r.status === 200,
        'homepage content delivered': (r) => r.body && r.body.length > 500,
    });
    if (!homeSuccess) errorCount.add(1);
    sleep(1);

    // 2. Sarpras Index (GET /sarpras)
    const sarprasRes = http.get(`${BASE_URL}/sarpras`, params);
    sarprasDuration.add(sarprasRes.timings.duration);
    const sarprasSuccess = check(sarprasRes, {
        'sarpras status is 200': (r) => r.status === 200,
    });
    if (!sarprasSuccess) errorCount.add(1);
    sleep(1);

    // 3. Sarpras Map (GET /sarpras/peta)
    const mapRes = http.get(`${BASE_URL}/sarpras/peta`, params);
    mapDuration.add(mapRes.timings.duration);
    const mapSuccess = check(mapRes, {
        'sarpras map status is 200': (r) => r.status === 200,
    });
    if (!mapSuccess) errorCount.add(1);
    sleep(1);

    // 4. Berita Index (GET /berita)
    const newsRes = http.get(`${BASE_URL}/berita`, params);
    newsDuration.add(newsRes.timings.duration);
    const newsSuccess = check(newsRes, {
        'news status is 200': (r) => r.status === 200,
    });
    if (!newsSuccess) errorCount.add(1);
    sleep(1.5);
}
