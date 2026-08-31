export type TextBlock = {
  title: string;
  body: string;
  href?: string;
};

// TODO(client): Replace future-work placeholders with confirmed plans, dates, and approved language.
export const futureWorkItems: TextBlock[] = [
  {
    title: "Voter outreach priorities",
    body: "Future campaign-cycle priorities, geography, and timing are pending client confirmation."
  },
  {
    title: "Candidate and event calendar",
    body: "Confirmed events and candidate-support activity should be added only after the organization approves public details."
  },
  {
    title: "Make a Voting Plan tool",
    body: "A future page will help Wisconsin voters plan how they prefer to vote and then direct them to official MyVote Wisconsin resources."
  }
];

export const volunteerAreas: TextBlock[] = [
  {
    title: "Canvassing",
    body: "Help with voter conversations once routes, training, and event details are approved."
  },
  {
    title: "Voter outreach",
    body: "Support practical voter-contact projects such as postcards, calls, and neighbor-to-neighbor follow-up."
  },
  {
    title: "Events",
    body: "Assist with setup, check-in, hospitality, logistics, and making local gatherings welcoming."
  },
  {
    title: "Operations",
    body: "Put useful behind-the-scenes skills to work on scheduling, supplies, communications, and coordination."
  }
];

export const socialPlaceholders: TextBlock[] = [
  {
    title: "Facebook",
    body: "Follow current organization updates on the client-provided Facebook profile.",
    href: "https://www.facebook.com/share/1ZQbJjCNyy/?mibextid=wwXIfr"
  },
  {
    title: "Instagram",
    body: "Follow approved photos and short updates on the client-provided Instagram profile.",
    href: "https://www.instagram.com/northshore_red?igsi=eXBscm0xdG1ocDQ5"
  },
  {
    title: "Event photos",
    body: "Reserved for organization-supplied photography after rights and captions are confirmed."
  }
];
