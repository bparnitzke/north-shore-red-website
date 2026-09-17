export type NavItem = {
  label: string;
  href: string;
  external?: boolean;
};

export const site = {
  name: "North Shore Red",
  url: "https://www.northshorered.com",
  description:
    "North Shore Red is a Wisconsin 501(c)(4) conservative grassroots organization promoting conservative principles across the North Shore, Milwaukee County, and at every level of government.",
  tagline: "Promoting conservative values\nAdvancing conservative leaders",
  taglineMobile: "Promoting conservative\nvalues in Wisconsin",
  logo: "/assets/north-shore-red-logo.png",
  logoLightBg: "/assets/north-shore-red-logo.jpg",
  donationUrl: "https://secure.winred.com/friends-of-north-shore-red/donate",
  myVoteUrl: "https://myvote.wi.gov/en-us/",
  // TODO(client): supply the voting-plan tool's own URL. Until then the nav
  // and CTAs fall back to the internal /make-a-plan/ page.
  votingPlanUrl: null as string | null,
  socialLinks: {
    facebook: "https://www.facebook.com/share/1ZQbJjCNyy/?mibextid=wwXIfr",
    instagram: "https://www.instagram.com/northshore_red?igsi=eXBscm0xdG1ocDQ5",
    x: "https://x.com/northshore_red"
  },
  legalDisclaimer:
    "North Shore Red is a Wisconsin-based 501(c)(4) conservative grassroots organization. Paid for by North Shore Red.",
  legalDisclaimerFull:
    "North Shore Red is a Wisconsin-based 501(c)(4) conservative grassroots organization. Paid for by North Shore Red. Fundraising disclosure and disclaimer pending counsel review.",
  noPayNote: "Nobody is paid for their efforts on behalf of North Shore Red. Contributions are not tax deductible."
};

export const primaryNav: NavItem[] = [
  { label: "Home", href: "/" },
  { label: "About us", href: "/about/" },
  { label: "Why donate to us", href: "/donate/" },
  { label: "Events", href: "/#next-event" },
  {
    label: "Make a voting plan",
    href: site.votingPlanUrl ?? "/make-a-plan/",
    external: Boolean(site.votingPlanUrl)
  },
  { label: "Contact us", href: "/contact/" }
];

export const footerNav = {
  site: [
    { label: "About", href: "/about/" },
    { label: "Contact us", href: "/contact/" },
    { label: "Events", href: "/#next-event" },
    { label: "Privacy", href: "/privacy-policy/" },
    { label: "Terms", href: "/terms-and-conditions/" }
  ] satisfies NavItem[],
  connect: [
    { label: "Facebook", href: site.socialLinks.facebook, external: true },
    { label: "Instagram", href: site.socialLinks.instagram, external: true },
    { label: "X", href: site.socialLinks.x, external: true }
  ] satisfies NavItem[]
};
