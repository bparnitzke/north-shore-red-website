export type InterestKey = "list" | "event" | "volunteer";

export const interestOptions: Array<{
  key: InterestKey;
  label: string;
  description: string;
}> = [
  {
    key: "list",
    label: "Join the general mailing list",
    description: "Occasional updates from the board on what the organization is doing."
  },
  {
    key: "event",
    label: "Join the fundraiser invitation list",
    description:
      "Our events are by invitation. A board member reviews every request, and invitations go out from that list."
  },
  {
    key: "volunteer",
    label: "Sign up to volunteer",
    description:
      "Canvassing, voter outreach, events or operations — tell us in the comments if you already know where you fit."
  }
];

// Maps the ?interest= query param to the checkbox that arrives pre-checked.
export const interestParamMap: Record<string, InterestKey> = {
  volunteer: "volunteer",
  event: "event"
};

export const successSocialCopy = {
  heading: "If you like our website, you'll love our social media accounts.",
  body:
    "Photos from the doors, candidate news and short updates from the board go up as they happen. It is the fastest way to see what we are doing this week."
};

export const successDonateCopy = {
  heading: "One more thing that helps right now",
  body:
    "We are raising money to extend our voter-contact program beyond greater Milwaukee. Every penny goes directly to reaching those voters."
};
