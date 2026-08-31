export type NavItem = {
  label: string;
  href: string;
  external?: boolean;
};

export type SocialLink = {
  label: string;
  href: string | null;
  status: "verified" | "pending";
};

export const site = {
  name: "North Shore Red",
  url: "https://www.northshorered.com",
  description:
    "North Shore Red is a Wisconsin-based 501(c)(4) conservative grassroots organization focused on practical volunteer action, voter outreach, events, and candidate support.",
  logo: "/assets/north-shore-red-logo.jpg",
  donationUrl: "https://secure.winred.com/friends-of-north-shore-red/donate",
  myVoteUrl: "https://myvote.wi.gov/en-us/",
  contact: {
    // TODO(client): Replace with the confirmed public contact email, phone, or contact route before launch.
    label: "Contact information pending client confirmation",
    href: "/volunteer/"
  },
  socialLinks: [
    {
      label: "Facebook",
      href: "https://www.facebook.com/share/1ZQbJjCNyy/?mibextid=wwXIfr",
      status: "verified"
    },
    {
      label: "Instagram",
      href: "https://www.instagram.com/northshore_red?igsi=eXBscm0xdG1ocDQ5",
      status: "verified"
    }
  ] satisfies SocialLink[],
  legalDisclaimer:
    "Legal and fundraising disclaimer placeholder. Counsel-approved language required before launch."
};

export const primaryNav: NavItem[] = [
  { label: "Home", href: "/" },
  { label: "About", href: "/about/" },
  { label: "Events", href: "/events/" },
  { label: "Volunteer", href: "/volunteer/" },
  { label: "Why Donate To Us", href: "/donate/" }
];

export const footerNav: NavItem[] = [
  { label: "Donate", href: site.donationUrl, external: true },
  { label: "Privacy Policy", href: "/privacy-policy/" },
  { label: "Terms and Conditions", href: "/terms-and-conditions/" },
  { label: "Make a Voting Plan", href: "/make-a-plan/" }
];
