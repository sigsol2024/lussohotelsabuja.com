<?php
/**
 * Site Settings Page
 */

$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';

// Get all settings
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings ORDER BY setting_key");
    $settingsRows = $stmt->fetchAll();
    $settings = [];
    foreach ($settingsRows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch(PDOException $e) {
    error_log("Settings page error: " . $e->getMessage());
    $settings = [];
}

$csrfToken = generateCSRFToken();
?>

<div class="page-intro">
    <h1>Site settings</h1>
    <p class="text-muted">Branding, footer, navigation, email, and integrations. Save to apply changes across the public site.</p>
</div>

<form id="settingsForm" class="settings-form">
    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
    
    <!-- General Settings -->
    <div class="card">
        <div class="card-header">
            <h2>General Settings</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="<?= sanitize($settings['site_name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="site_tagline">Site Tagline</label>
                <input type="text" id="site_tagline" name="site_tagline" value="<?= sanitize($settings['site_tagline'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="room_detail_hero_badge">Room detail hero badge</label>
                <input type="text" id="room_detail_hero_badge" name="room_detail_hero_badge" value="<?= sanitize($settings['room_detail_hero_badge'] ?? 'Lusso Abuja') ?>" placeholder="Lusso Abuja">
                <p class="form-help">Small uppercase label above the room title on single-room pages.</p>
            </div>
            
            <div class="form-group">
                <label for="currency_symbol">Currency Symbol</label>
                <input type="text" id="currency_symbol" name="currency_symbol" value="<?= sanitize($settings['currency_symbol'] ?? '$') ?>" placeholder="$" maxlength="5">
                <p class="form-help">Currency symbol used throughout the site (e.g., $, ₦, €, £). Default: $</p>
            </div>
            
            <div class="form-group">
                <label>Logo — dark variant (header, light backgrounds)</label>
                <p class="form-help">Coffee brown / dark artwork for use on off-white (#efe8d6) header. Recommended file: <code>assets/images/logo/logo-dark.png</code> (optional fallback if file exists and this field is empty).</p>
                <div style="margin-bottom: 10px;">
                    <button type="button" class="btn btn-outline" onclick="openMediaModal('site_logo', 'logo_preview')">
                        <i class="fas fa-image"></i> Select dark logo
                    </button>
                </div>
                <input type="hidden" id="site_logo" name="site_logo" value="<?= sanitize($settings['site_logo'] ?? '') ?>">
                <div id="logo_preview" class="image-preview" style="margin-top: 10px; <?= !empty($settings['site_logo']) ? 'display: block;' : 'display: none;' ?>">
                    <?php if (!empty($settings['site_logo'])): ?>
                        <img id="logo_img" src="<?= SITE_URL . ltrim($settings['site_logo'], '/') ?>" alt="" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                    <?php else: ?>
                        <img id="logo_img" src="" alt="" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Logo — light variant (footer, dark / primary background)</label>
                <p class="form-help">White or off-white (#efe8d6) artwork for brown (#411d13) footer. Do not use the dark header logo here. Optional file fallback: <code>assets/images/logo/logo-light.png</code>.</p>
                <div style="margin-bottom: 10px;">
                    <button type="button" class="btn btn-outline" onclick="openMediaModal('site_logo_light', 'logo_light_preview')">
                        <i class="fas fa-image"></i> Select light logo
                    </button>
                </div>
                <input type="hidden" id="site_logo_light" name="site_logo_light" value="<?= sanitize($settings['site_logo_light'] ?? '') ?>">
                <div id="logo_light_preview" class="image-preview" style="margin-top: 10px; <?= !empty($settings['site_logo_light']) ? 'display: block;' : 'display: none;' ?>">
                    <?php if (!empty($settings['site_logo_light'])): ?>
                        <img id="logo_light_img" src="<?= SITE_URL . ltrim($settings['site_logo_light'], '/') ?>" alt="" style="max-width: 200px; max-height: 200px; object-fit: contain; background: #411d13; padding: 8px;">
                    <?php else: ?>
                        <img id="logo_light_img" src="" alt="" style="max-width: 200px; max-height: 200px; object-fit: contain; background: #411d13; padding: 8px;">
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Favicon</label>
                <p class="form-help">Simplified mark, ideally 32×32 or 64×64 PNG. If empty, <code>assets/images/logo/favicon.png</code> is used when present.</p>
                <div style="margin-bottom: 10px;">
                    <button type="button" class="btn btn-outline" onclick="openMediaModal('site_favicon', 'favicon_preview')">
                        <i class="fas fa-image"></i> Select Favicon
                    </button>
                </div>
                <input type="hidden" id="site_favicon" name="site_favicon" value="<?= sanitize($settings['site_favicon'] ?? '') ?>">
                <div id="favicon_preview" class="image-preview" style="margin-top: 10px; <?= !empty($settings['site_favicon']) ? 'display: block;' : 'display: none;' ?>">
                    <?php if (!empty($settings['site_favicon'])): ?>
                        <img id="favicon_img" src="<?= SITE_URL . ltrim($settings['site_favicon'], '/') ?>" style="max-width: 64px; max-height: 64px;">
                    <?php else: ?>
                        <img id="favicon_img" src="" style="max-width: 64px; max-height: 64px;">
                    <?php endif; ?>
                </div>
                <p class="form-help">Select an image from the media library or upload a new one</p>
            </div>
        </div>
    </div>

    <!-- Header CTA (desktop nav button) -->
    <div class="card">
        <div class="card-header">
            <h2>Header — primary button</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="nav_cta_label">Button label</label>
                <input type="text" id="nav_cta_label" name="nav_cta_label" value="<?= sanitize($settings['nav_cta_label'] ?? 'Check Availability') ?>" placeholder="Check Availability">
                <p class="form-help">Shown on the right side of the desktop header (e.g. Check Availability).</p>
            </div>
            <div class="form-group">
                <label for="nav_cta_href">Button URL</label>
                <input type="text" id="nav_cta_href" name="nav_cta_href" value="<?= sanitize($settings['nav_cta_href'] ?? '/rooms') ?>" placeholder="/rooms or https://booking.example.com/...">
                <p class="form-help">Internal path (e.g. <code>/rooms</code>) or full booking engine URL. This replaces the old fixed “contact” link for that button.</p>
            </div>
        </div>
    </div>
    
    <!-- Footer Settings -->
    <div class="card">
        <div class="card-header">
            <h2>Footer Settings</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="footer_copyright">Copyright Text</label>
                <input type="text" id="footer_copyright" name="footer_copyright" value="<?= sanitize($settings['footer_copyright'] ?? '') ?>">
                <p class="form-help">This will be shown as: Copyright © [Year] [Your Text]. All rights reserved.</p>
            </div>
            
            <div class="form-group">
                <label for="footer_address">Address</label>
                <textarea id="footer_address" name="footer_address" rows="2"><?= sanitize($settings['footer_address'] ?? '123 Luxury Blvd, Malibu, CA 90265') ?></textarea>
                <p class="form-help">Physical address displayed in the footer contact section</p>
            </div>
            
            <div class="form-group">
                <label for="footer_phone">Phone Number(s)</label>
                <input type="text" id="footer_phone" name="footer_phone" value="<?= sanitize($settings['footer_phone'] ?? '+1 (555) 123-4567') ?>">
                <p class="form-help">Enter phone number(s) to display in the footer (e.g., +234 813 480 7718 | +234 907 676 0923)</p>
            </div>
            
            <div class="form-group">
                <label for="footer_email">Email Address</label>
                <input type="email" id="footer_email" name="footer_email" value="<?= sanitize($settings['footer_email'] ?? 'concierge@lusso.com') ?>">
                <p class="form-help">Email address displayed in the footer contact section</p>
            </div>
            
            <div class="form-group">
                <label for="contact_email">Contact Form Email</label>
                <input type="email" id="contact_email" name="contact_email" value="<?= sanitize($settings['contact_email'] ?? $settings['footer_email'] ?? 'concierge@lusso.com') ?>">
                <p class="form-help">Email address where contact form submissions will be sent</p>
            </div>
            
            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Number</label>
                <input type="text" id="whatsapp_number" name="whatsapp_number" value="<?= sanitize($settings['whatsapp_number'] ?? '') ?>" placeholder="+2341234567890">
                <p class="form-help">WhatsApp number with country code (e.g., +2341234567890). This will be used for the WhatsApp button on the contact page.</p>
            </div>
        </div>
    </div>
    
    <!-- SMTP Settings -->
    <div class="card">
        <div class="card-header">
            <h2>SMTP Email Settings</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="alert-smtp-note">
                <strong>Note:</strong> Configure SMTP to send contact form messages. Without SMTP, submissions are not emailed.
            </div>
            
            <div class="form-group">
                <label for="smtp_host">SMTP Host</label>
                <input type="text" id="smtp_host" name="smtp_host" value="<?= sanitize($settings['smtp_host'] ?? '') ?>" placeholder="smtp.gmail.com">
                <p class="form-help">SMTP server hostname (e.g., smtp.gmail.com, smtp.mailtrap.io)</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_port">SMTP Port</label>
                <input type="number" id="smtp_port" name="smtp_port" value="<?= sanitize($settings['smtp_port'] ?? '587') ?>" placeholder="587">
                <p class="form-help">SMTP port (usually 587 for TLS, 465 for SSL, 25 for unencrypted)</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_username">SMTP Username</label>
                <input type="text" id="smtp_username" name="smtp_username" value="<?= sanitize($settings['smtp_username'] ?? '') ?>" placeholder="your-email@gmail.com">
                <p class="form-help">SMTP authentication username (usually your email address)</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_password">SMTP Password</label>
                <input type="password" id="smtp_password" name="smtp_password" value="<?= sanitize($settings['smtp_password'] ?? '') ?>" placeholder="Your SMTP password">
                <p class="form-help">SMTP authentication password (for Gmail, use an App Password)</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_encryption">SMTP Encryption</label>
                <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                    <option value="tls" <?= ($settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= ($settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                    <option value="" <?= empty($settings['smtp_encryption']) ? 'selected' : '' ?>>None</option>
                </select>
                <p class="form-help">Encryption method (TLS for port 587, SSL for port 465)</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_from_email">From Email</label>
                <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?= sanitize($settings['smtp_from_email'] ?? $settings['contact_email'] ?? '') ?>" placeholder="noreply@yourdomain.com">
                <p class="form-help">Email address that will appear as the sender</p>
            </div>
            
            <div class="form-group">
                <label for="smtp_from_name">From Name</label>
                <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?= sanitize($settings['smtp_from_name'] ?? ($settings['site_name'] ?? 'Lusso Hotels')) ?>" placeholder="Site / hotel name">
                <p class="form-help">Name that will appear as the sender</p>
            </div>
        </div>
    </div>
    
    <!-- Social Media Links -->
    <div class="card">
        <div class="card-header">
            <h2>Social Media Links</h2>
        </div>
        <div class="card-body card-body--stack">
            <div id="socialMediaList">
                <!-- Social media items will be rendered here -->
            </div>
            <button type="button" class="btn btn-outline" onclick="addSocialMedia()" style="margin-top: 12px;">
                <i class="fas fa-plus"></i> Add Social Media
            </button>
            <input type="hidden" id="social_media_json" name="social_media_json" value="<?= htmlspecialchars($settings['social_media_json'] ?? '[]', ENT_QUOTES, 'UTF-8') ?>">
        </div>
    </div>

    <!-- Live Chat -->
    <div class="card">
        <div class="card-header">
            <h2>Live chat</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="smartsupp_key">Smartsupp key</label>
                <input type="text" id="smartsupp_key" name="smartsupp_key" value="<?= sanitize($settings['smartsupp_key'] ?? '') ?>" placeholder="Your Smartsupp key">
                <p class="form-help">If set, Smartsupp will load on every public page. You can change this later when your client account is ready.</p>
            </div>
        </div>
    </div>
    
    <!-- Google Maps Settings -->
    <div class="card">
        <div class="card-header">
            <h2>Google Maps</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="google_maps_api_key">Google Maps API Key</label>
                <input type="text" id="google_maps_api_key" name="google_maps_api_key" value="<?= sanitize($settings['google_maps_api_key'] ?? '') ?>" placeholder="AIzaSy...">
                <p class="form-help">Enter your Google Maps API key to enable interactive maps on the contact page. Get your API key from <a href="https://console.cloud.google.com/" target="_blank" rel="noopener">Google Cloud Console</a>. The contact page will show a static placeholder image until an API key is configured.</p>
            </div>
        </div>
    </div>
    
    <!-- Custom Scripts -->
    <div class="card">
        <div class="card-header">
            <h2>Custom Scripts</h2>
        </div>
        <div class="card-body card-body--stack">
            <div class="form-group">
                <label for="header_scripts">Header Scripts</label>
                <textarea id="header_scripts" name="header_scripts" rows="6" class="mono"><?= htmlspecialchars($settings['header_scripts'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <p class="form-help">Scripts will be added in the &lt;head&gt; section (e.g., Google Analytics, Meta Pixel, etc.)</p>
            </div>
            
            <div class="form-group">
                <label for="body_scripts">Body Scripts</label>
                <textarea id="body_scripts" name="body_scripts" rows="6" class="mono"><?= htmlspecialchars($settings['body_scripts'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <p class="form-help">Scripts will be added right after the opening &lt;body&gt; tag (e.g., chat widgets, tracking scripts)</p>
            </div>
            
            <div class="form-group">
                <label for="footer_scripts">Footer Scripts</label>
                <textarea id="footer_scripts" name="footer_scripts" rows="6" class="mono"><?= htmlspecialchars($settings['footer_scripts'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                <p class="form-help">Scripts will be added right before the closing &lt;/body&gt; tag (e.g., analytics, custom JavaScript)</p>
            </div>
        </div>
    </div>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save settings</button>
        <span class="text-muted">All sections above are saved together.</span>
    </div>
</form>

<script>
// Media modal integration - the openMediaModal function is provided by media-library.js

// Social Media Management
let socialMediaList = [];

const SOCIAL_PLATFORMS = [
    { value: 'facebook', label: 'Facebook' },
    { value: 'instagram', label: 'Instagram' },
    { value: 'tiktok', label: 'TikTok' },
    { value: 'x', label: 'X (formerly Twitter)' }
];

function inferPlatformFromUrl(url) {
    const u = String(url || '').toLowerCase();
    if (u.includes('instagram.com')) return 'instagram';
    if (u.includes('tiktok.com')) return 'tiktok';
    if (u.includes('twitter.com') || u.includes('x.com')) return 'x';
    if (u.includes('facebook.com') || u.includes('fb.com')) return 'facebook';
    return 'facebook';
}

function normalizePlatform(p) {
    const v = String(p || '').toLowerCase().trim();
    if (v === 'twitter' || v === 'x-twitter') return 'x';
    if (v === 'ig') return 'instagram';
    return v || 'facebook';
}

// Load social media from hidden input
function loadSocialMedia() {
    const jsonInput = document.getElementById('social_media_json');
    try {
        socialMediaList = JSON.parse(jsonInput.value || '[]');
    } catch (e) {
        console.error('Error parsing social media JSON:', e);
        socialMediaList = [];
    }

    // Backwards compatibility: older entries may be { icon, url }.
    // Convert to { platform, url } by inferring from URL.
    if (!Array.isArray(socialMediaList)) socialMediaList = [];
    socialMediaList = socialMediaList.map((item) => {
        const url = (item && typeof item === 'object') ? (item.url || '') : '';
        const platform = (item && typeof item === 'object') ? (item.platform || '') : '';
        return {
            platform: normalizePlatform(platform || inferPlatformFromUrl(url)),
            url: String(url || '')
        };
    });
    renderSocialMedia();
}

// Render social media list
function renderSocialMedia() {
    const container = document.getElementById('socialMediaList');
    container.innerHTML = '';
    
    if (socialMediaList.length === 0) {
        container.innerHTML = '<p class="form-help" style="margin-bottom: 15px;">No social media links added yet. Click "Add Social Media" to add one.</p>';
    } else {
        socialMediaList.forEach((item, index) => {
            const div = document.createElement('div');
            div.className = 'social-item';

            const optionsHtml = SOCIAL_PLATFORMS.map((p) => {
                const selected = normalizePlatform(item.platform) === p.value ? 'selected' : '';
                return `<option value="${p.value}" ${selected}>${p.label}</option>`;
            }).join('');

            div.innerHTML = `
                <div class="social-item__head">
                    <strong>Social link #${index + 1}</strong>
                    <button type="button" class="btn btn-sm btn-outline" onclick="removeSocialMedia(${index})">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
                <div class="form-group" style="margin-bottom: 12px;">
                    <label>Platform</label>
                    <select onchange="updateSocialMedia(${index}, 'platform', this.value)">
                        ${optionsHtml}
                    </select>
                    <p class="form-help">Select the platform — the correct icon will be shown in the footer automatically.</p>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>URL</label>
                    <input type="url" value="${(item.url || '').replace(/"/g, '&quot;').replace(/'/g, '&#39;')}" 
                           onchange="updateSocialMedia(${index}, 'url', this.value)" 
                           placeholder="https://...">
                    <p class="form-help">Full profile or page URL</p>
                </div>
            `;
            container.appendChild(div);
        });
    }
    
    // Update hidden input
    document.getElementById('social_media_json').value = JSON.stringify(socialMediaList);
}

// Add new social media
function addSocialMedia() {
    socialMediaList.push({
        platform: 'facebook',
        url: ''
    });
    renderSocialMedia();
}

// Remove social media
function removeSocialMedia(index) {
    if (confirm('Are you sure you want to remove this social media link?')) {
        socialMediaList.splice(index, 1);
        renderSocialMedia();
    }
}

// Update social media item
function updateSocialMedia(index, field, value) {
    if (socialMediaList[index]) {
        if (field === 'platform') {
            socialMediaList[index][field] = normalizePlatform(value);
        } else {
            socialMediaList[index][field] = value;
        }
        document.getElementById('social_media_json').value = JSON.stringify(socialMediaList);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSocialMedia();
});

// Form submission
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Ensure social media JSON is up to date & normalized
    const dedup = new Map();
    (socialMediaList || []).forEach((item) => {
        const platform = normalizePlatform(item?.platform);
        const url = String(item?.url || '').trim();
        if (!url) return;
        dedup.set(platform, { platform, url });
    });
    socialMediaList = Array.from(dedup.values());
    document.getElementById('social_media_json').value = JSON.stringify(socialMediaList);
    
    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => {
        if (key !== 'csrf_token') {
            data[key] = value;
        }
    });
    
    const submitBtn = this.querySelector('button[type="submit"]');
    if (typeof setSaveButtonSavingState === 'function') setSaveButtonSavingState(submitBtn, true);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    fetch('<?= ADMIN_URL ?>api/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status + ': ' + response.statusText);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Server returned invalid response. Please check server logs.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Operation failed', 'error');
        }
    })
    .catch(error => {
        console.error('Settings save error:', error);
        showToast('Error: ' + error.message, 'error');
    })
    .finally(() => {
        if (typeof setSaveButtonSavingState === 'function') setSaveButtonSavingState(submitBtn, false);
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

