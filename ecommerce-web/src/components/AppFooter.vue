<template>
    <footer class="main-footer">
        <!-- Top CTA Strip -->
        <div class="footer-strip">
            <div class="footer-strip-inner">
                <div class="strip-text">
                    <q-icon name="storefront" size="20px" color="amber" class="q-mr-sm" />
                    <span>Discover local shops near you — <strong>start exploring today</strong></span>
                </div>
                <router-link to="/find-shops" class="strip-btn">
                    <q-icon name="near_me" size="16px" />
                    <span>Find Shops</span>
                </router-link>
            </div>
        </div>

        <!-- Main Footer Body -->
        <div class="footer-body">
            <div class="footer-inner">

                <!-- Brand Column -->
                <div class="footer-brand">
                    <router-link to="/" class="footer-logo-link">
                        <div class="footer-logo-wrap">
                            <BiliscartLogo :size="38" />
                        </div>
                        <div class="footer-brand-text">
                            <div class="footer-brand-name">My Near Shops</div>
                            <div class="footer-brand-tagline">Your Community Marketplace</div>
                        </div>
                    </router-link>
                    <p class="footer-desc">
                        Connecting you with hundreds of trusted local shops. Fresh products, great prices, and fast
                        delivery — all in one place.
                    </p>
                    <div class="footer-badges">
                        <div class="footer-badge"><q-icon name="verified" size="14px" color="positive" /><span>Verified
                                Shops</span></div>
                        <div class="footer-badge"><q-icon name="lock" size="14px" color="positive" /><span>Secure
                                Payments</span></div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col">
                    <div class="footer-col-title">Explore</div>
                    <router-link to="/" class="footer-link">
                        <q-icon name="home" size="14px" /><span>Home</span>
                    </router-link>
                    <router-link to="/find-shops" class="footer-link">
                        <q-icon name="storefront" size="14px" /><span>Find Shops</span>
                    </router-link>
                    <router-link to="/find-items" class="footer-link">
                        <q-icon name="search" size="14px" /><span>Browse Items</span>
                    </router-link>
                    <router-link to="/cart" class="footer-link">
                        <q-icon name="shopping_cart" size="14px" /><span>My Cart</span>
                    </router-link>
                </div>

                <!-- Account -->
                <div class="footer-col">
                    <div class="footer-col-title">Account</div>
                    <router-link to="/login" class="footer-link" v-if="!profile.token">
                        <q-icon name="login" size="14px" /><span>Sign In</span>
                    </router-link>
                    <router-link to="/register" class="footer-link" v-if="!profile.token">
                        <q-icon name="person_add" size="14px" /><span>Create Account</span>
                    </router-link>
                    <router-link to="/dashboard" class="footer-link" v-if="profile.token">
                        <q-icon name="dashboard" size="14px" /><span>Dashboard</span>
                    </router-link>
                    <router-link to="/cart" class="footer-link">
                        <q-icon name="receipt_long" size="14px" /><span>My Orders</span>
                    </router-link>
                </div>

                <!-- Contact -->
                <div class="footer-col">
                    <div class="footer-col-title">Get in Touch</div>
                    <div class="footer-contact-item">
                        <div class="contact-icon"><q-icon name="email" size="14px" color="white" /></div>
                        <div>
                            <div class="contact-label">Email</div>
                            <div class="contact-value">admin@mynearshops.com</div>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="contact-icon"><q-icon name="location_on" size="14px" color="white" /></div>
                        <div>
                            <div class="contact-label">Location</div>
                            <div class="contact-value">Philippines</div>
                        </div>
                    </div>
                    <div class="footer-contact-item">
                        <div class="contact-icon"><q-icon name="schedule" size="14px" color="white" /></div>
                        <div>
                            <div class="contact-label">Support Hours</div>
                            <div class="contact-value">24/7 Available</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div class="footer-bottom-inner">
                <div class="footer-copyright">
                    &copy; {{ new Date().getFullYear() }} My Near Shops. All rights reserved.
                </div>
                <div class="footer-social">
                    <a href="#" class="social-icon" aria-label="Facebook">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.93-1.956 1.886v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Twitter / X">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                        </svg>
                    </a>
                    <a href="#" class="social-icon" aria-label="Instagram">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { useUserStore } from 'src/stores/user';
import BiliscartLogo from 'src/components/BiliscartLogo.vue';

const { profile } = storeToRefs(useUserStore());
</script>

<style scoped lang="scss">
// ── Base ──────────────────────────────────────────────────────────────────────
.main-footer {
    background: linear-gradient(180deg, #1e1b4b 0%, #0f0c29 100%);
    color: rgba(255, 255, 255, 0.75);
    font-size: 14px;
}

// ── Top CTA Strip ─────────────────────────────────────────────────────────────
.footer-strip {
    background: rgba(99, 102, 241, 0.18);
    border-top: 1px solid rgba(99, 102, 241, 0.25);
    border-bottom: 1px solid rgba(99, 102, 241, 0.15);
    padding: 14px 28px;
}

.footer-strip-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.strip-text {
    display: flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.85);
    font-size: 14px;
    font-weight: 500;

    strong {
        color: white;
    }
}

.strip-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    border-radius: 24px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);

    &:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(251, 191, 36, 0.4);
    }
}

// ── Main Body ─────────────────────────────────────────────────────────────────
.footer-body {
    padding: 64px 28px 48px;
}

.footer-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 2.2fr 1fr 1fr 1.4fr;
    gap: 48px;
    align-items: start;
}

// ── Brand Column ──────────────────────────────────────────────────────────────
.footer-brand {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.footer-logo-link {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
}

.footer-logo-wrap {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    background: linear-gradient(135deg, #312e81 0%, #4c1d95 100%);
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    padding: 4px;
}

.footer-brand-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.footer-brand-name {
    font-size: 17px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.3px;
    line-height: 1.2;
}

.footer-brand-tagline {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.45);
    font-weight: 500;
    letter-spacing: 0.2px;
}

.footer-desc {
    font-size: 14px;
    line-height: 1.75;
    color: rgba(255, 255, 255, 0.55);
    margin: 0;
    max-width: 320px;
}

.footer-badges {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.footer-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
}

// ── Link Columns ──────────────────────────────────────────────────────────────
.footer-col {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.footer-col-title {
    font-size: 12px;
    font-weight: 800;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: rgba(255, 255, 255, 0.55);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    padding: 7px 0;
    transition: all 0.2s ease;
    border-radius: 6px;

    &:hover {
        color: white;
        transform: translateX(4px);

        .q-icon {
            color: #fbbf24 !important;
        }
    }
}

// ── Contact Column ────────────────────────────────────────────────────────────
.footer-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 8px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);

    &:last-child {
        border-bottom: none;
    }
}

.contact-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    background: rgba(99, 102, 241, 0.25);
    border: 1px solid rgba(99, 102, 241, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
}

.contact-label {
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.35);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1;
    margin-bottom: 3px;
}

.contact-value {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.7);
    font-weight: 500;
    line-height: 1.3;
}

// ── Bottom Bar ────────────────────────────────────────────────────────────────
.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.07);
    padding: 20px 28px;
}

.footer-bottom-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.footer-copyright {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.35);
    font-weight: 500;
}

.footer-social {
    display: flex;
    gap: 8px;
}

.social-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    transition: all 0.22s ease;

    &:hover {
        background: rgba(99, 102, 241, 0.3);
        border-color: rgba(99, 102, 241, 0.5);
        color: white;
        transform: translateY(-2px);
    }
}

// ── Responsive ────────────────────────────────────────────────────────────────
@media (max-width: 1024px) {
    .footer-inner {
        grid-template-columns: 1fr 1fr;
        gap: 36px;
    }

    .footer-brand {
        grid-column: span 2;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 24px;
    }

    .footer-desc {
        max-width: 100%;
    }
}

@media (max-width: 640px) {
    .footer-body {
        padding: 40px 20px 36px;
    }

    .footer-bottom {
        padding: 16px 20px;
    }

    .footer-strip {
        padding: 12px 20px;
    }

    .footer-inner {
        grid-template-columns: 1fr 1fr;
        gap: 28px;
    }

    .footer-brand {
        grid-column: span 2;
        flex-direction: column;
    }

    .footer-bottom-inner {
        flex-direction: column;
        text-align: center;
    }

    .strip-text {
        font-size: 13px;
    }
}

@media (max-width: 420px) {
    .footer-inner {
        grid-template-columns: 1fr;
    }

    .footer-brand {
        grid-column: span 1;
    }
}
</style>
