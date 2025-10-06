class ClickTracker {
    constructor() {
        this.websiteId = 'j0IQx5DpYT8Wr4mGH5XBBzeBVemaxjRe';
        this.endpoint = 'http://localhost:8080/api/clicks';
        this.clickCount = 0;
        this.init();
    }

    init() {
        if (!this.websiteId) {
            console.warn('ClickTracker: Website ID not configured');
            return;
        }

        console.log('ClickTracker: Initialized for website', this.websiteId);

        document.addEventListener('click', (event) => {
            this.trackClick(event);
        });

        window.addEventListener('beforeunload', () => {
            this.sendBatchData();
        });
    }

    trackClick(event) {
        const clickData = {
            website_id: this.websiteId,
            x: event.clientX,
            y: event.clientY,
            timestamp: new Date().toISOString(),
            url: window.location.href,
            viewport: {
                width: window.innerWidth,
                height: window.innerHeight
            }
        };

        this.storeClick(clickData);
        this.clickCount++;
    }

    storeClick(clickData) {
        let clicks = JSON.parse(localStorage.getItem('click_tracker_data') || '[]');
        clicks.push(clickData);
        localStorage.setItem('click_tracker_data', JSON.stringify(clicks));

        if (clicks.length >= 5) {
            this.sendBatchData();
        }
    }

    async sendBatchData() {
        const clicks = JSON.parse(localStorage.getItem('click_tracker_data') || '[]');
        if (clicks.length === 0) return;

        try {
            const payload = {
                clicks: clicks,
                metadata: {
                    total_clicks: this.clickCount,
                    timestamp: new Date().toISOString()
                }
            };

            const response = await fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload)
            });

            if (response.ok) {
                console.log('ClickTracker: Data sent successfully, clicks:', clicks.length);
                localStorage.removeItem('click_tracker_data');
            } else {
                console.error('ClickTracker: Failed to send data', response.status);
            }
        } catch (error) {
            console.error('ClickTracker: Failed to send data', error);
        }
    }

    getStats() {
        const pendingClicks = JSON.parse(localStorage.getItem('click_tracker_data') || '[]').length;
        return {
            totalClicks: this.clickCount,
            pendingClicks: pendingClicks
        };
    }
}

function initializeClickTracker() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initTracker();
        });
    } else {
        initTracker();
    }

    function initTracker() {
        console.log('ClickTracker initialized');
        window.clickTracker = new ClickTracker();
    }
}

initializeClickTracker();