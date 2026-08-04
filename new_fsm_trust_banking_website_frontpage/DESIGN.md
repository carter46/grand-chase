---
name: Institutional Trust Design System
colors:
  surface: '#f9f9f9'
  surface-dim: '#dadada'
  surface-bright: '#f9f9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f3f3'
  surface-container: '#eeeeee'
  surface-container-high: '#e8e8e8'
  surface-container-highest: '#e2e2e2'
  on-surface: '#1a1c1c'
  on-surface-variant: '#5d4038'
  inverse-surface: '#2f3131'
  inverse-on-surface: '#f1f1f1'
  outline: '#916f66'
  outline-variant: '#e6bdb2'
  surface-tint: '#b02f00'
  primary: '#ac2e00'
  on-primary: '#ffffff'
  primary-container: '#d73b00'
  on-primary-container: '#fffbff'
  inverse-primary: '#ffb5a0'
  secondary: '#5c5e64'
  on-secondary: '#ffffff'
  secondary-container: '#dedfe6'
  on-secondary-container: '#606368'
  tertiary: '#5a5c5c'
  on-tertiary: '#ffffff'
  tertiary-container: '#737575'
  on-tertiary-container: '#fcfcfc'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbd1'
  primary-fixed-dim: '#ffb5a0'
  on-primary-fixed: '#3b0900'
  on-primary-fixed-variant: '#862200'
  secondary-fixed: '#e1e2e9'
  secondary-fixed-dim: '#c4c6cd'
  on-secondary-fixed: '#191c21'
  on-secondary-fixed-variant: '#44474c'
  tertiary-fixed: '#e2e2e2'
  tertiary-fixed-dim: '#c6c6c7'
  on-tertiary-fixed: '#1a1c1c'
  on-tertiary-fixed-variant: '#454747'
  background: '#f9f9f9'
  on-background: '#1a1c1c'
  surface-variant: '#e2e2e2'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 40px
    fontWeight: '700'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '700'
    lineHeight: 16px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 16px
  caption:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 16px
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1200px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
---

## Brand & Style

This design system is built upon the principles of **Institutional Reliability** and **High-Contrast Clarity**. It is designed for a traditional corporate banking environment where trust, stability, and precision are paramount. The aesthetic avoids all "fintech" trends—such as excessive rounding, glassmorphism, or vibrant gradients—in favor of a structured, authoritative presence.

The visual language utilizes a **Corporate Modern** style:
- **Professionalism:** Strict adherence to a grid and geometric alignment.
- **Trustworthiness:** High-contrast color pairings that ensure legibility and accessibility.
- **Efficiency:** A utilitarian approach to interface elements, prioritizing speed of information processing over decorative flair.
- **Legacy:** Drawing from traditional banking hallmarks—structured layouts and substantial, high-quality typography.

## Colors

The palette is anchored by the high-energy Primary Orange, used sparingly as a functional signal for primary actions and brand identification.

- **Primary Orange (#FF4800):** Used exclusively for primary call-to-action buttons, key brand highlights, and critical interactive states.
- **Dark Charcoal (#25282D):** The foundation for headers, footers, and secondary navigation elements. It provides the "weight" necessary for a banking institution.
- **White (#FFFFFF):** The primary surface color for all content containers to maintain a clean, high-contrast environment.
- **Light Gray (#F5F5F5):** Used for large background areas to provide subtle separation between white content cards.
- **Text Primary (#333333):** Optimized for long-form readability and data display, ensuring a softer but clear contrast compared to pure black.

## Typography

The design system utilizes **Inter** for its exceptional legibility and neutral, professional character. 

- **Hierarchy:** We use a strict weight contrast. Headings are always Bold (700) or Semi-Bold (600) to project authority. 
- **Readability:** Body text is set at a Regular (400) weight with generous line heights to ensure financial data is easily digestible.
- **Micro-copy:** Labels for inputs and data headers use a Medium (500) weight or Bold (700) with uppercase styling to differentiate them from interactive content.
- **Color:** Headlines should default to Dark Charcoal (#25282D), while body text remains at Text Primary (#333333).

## Layout & Spacing

This design system employs a **Fixed Grid** model for desktop and a **Fluid Grid** for mobile.

- **Grid System:** A 12-column grid is used for desktop (1200px max width) with 24px gutters. Elements should align strictly to column edges.
- **Rhythm:** An 8px linear spacing scale governs all internal padding and margins. 
- **White Space:** Information density should be moderate. Use generous vertical "stack" spacing (32px+) between major sections to prevent the UI from feeling cluttered, which can lead to user anxiety in financial contexts.
- **Mobile:** On mobile devices, margins shrink to 16px. Columns collapse into a single vertical stack, and complex data tables should transition into a "card-row" format.

## Elevation & Depth

To maintain a traditional, sturdy feel, the design system utilizes **Tonal Layers** and **Minimal Shadows**.

- **Surfaces:** Use color to define depth rather than shadows. Primary content sits on White (#FFFFFF) surfaces, while the background is Light Gray (#F5F5F5).
- **Shadows:** Only use shadows for "Floating" elements like dropdown menus or modals. These shadows should be tight, dark, and low-blur (e.g., `0px 2px 4px rgba(0,0,0,0.1)`) to feel grounded and physical rather than ethereal.
- **Outlines:** Use 1px solid borders in #E0E0E0 for input fields and card containers. This adds structural definition without the softness of shadows.

## Shapes

The shape language is **Rectilinear**.

- **Corners:** A subtle 0.25rem (4px) border radius is applied to buttons, input fields, and containers. This "softened square" approach maintains a serious, corporate tone while feeling modern enough for digital screens.
- **Strictness:** Icons and decorative elements should avoid circular containers unless specifically used for avatars or status indicators.

## Components

### Buttons
- **Primary:** Background #FF4800, Text #FFFFFF. Rectangular with 4px radius. No gradient. Bold uppercase text.
- **Secondary:** Background #25282D, Text #FFFFFF. Used for secondary actions like "Cancel" or "Sign Up".
- **Outline:** Transparent background, 1px border #25282D, Text #25282D.

### Input Fields
- **Default:** 1px border #E0E0E0, 4px radius, White background.
- **Focus:** 1px border #FF4800. No outer glow.
- **Labels:** Use `label-bold` (14px Bold, Uppercase) sitting above the field.

### Cards
- **Structure:** White background, 1px border #E0E0E0, no shadow. 
- **Padding:** 24px internal padding for all desktop cards.

### Data Tables
- **Header:** Dark Charcoal (#25282D) background with White text or Light Gray (#F5F5F5) with Text Primary.
- **Rows:** Alternating "Zebra" stripes (White and Light Gray) are encouraged for high-density financial data.

### Chips/Tags
- **Style:** Rectangular, Light Gray (#F5F5F5) background, Text Primary (#333333). Used for status indicators (e.g., "Pending", "Cleared").