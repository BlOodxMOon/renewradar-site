---
{
 "slug": "is-renewradar-safe",
 "date": "2026-12-25",
 "title": "Is RenewRadar Safe? How Your Data Stays on Your Phone",
 "coverTitle": "Is RenewRadar safe?",
 "seoTitle": "Is RenewRadar Safe? Privacy and Data Explained",
 "description": "What data RenewRadar stores, where it is kept, what the app sends over the internet and what permissions it needs. A plain explanation of the app's privacy.",
 "tag": "Privacy",
 "lede": "Before you add your subscriptions to any app, it is fair to ask what happens to that information. Here is a plain answer for RenewRadar.",
 "cover": { "kicker": "Privacy and safety", "screen": "home" },
 "figures": {
  "a": { "type": "compare", "heading": "What RenewRadar does and does not do", "columns": ["RenewRadar"], "rows": [["Asks for bank or card details", "Never"], ["Requires an account", "No"], ["Stores your list on a server", "No, only on your phone"], ["Shows ads or uses trackers", "No"], ["Sells or shares your data", "No"], ["Works offline", "Yes"]] },
  "b": { "type": "compare", "heading": "What RenewRadar asks your phone for", "columns": ["Asked for"], "rows": [["Notifications, to show reminders", "yes"], ["Location", "no"], ["Contacts", "no"], ["Camera or microphone", "no"], ["Photos and files", "no"]] }
 },
 "related": ["subscription-tracker-without-bank-login", "subscription-tracker-no-account", "/private-subscription-tracker/"]
}
---
RenewRadar is a subscription tracker and reminder app for Android made by Pixel DevCode. Your subscription list says a lot about you, so we designed the app to know as little as possible about you. Here is exactly how it works.

## What you enter, and where it is stored

Everything you add, such as names, prices, dates, categories and notes, is stored in the app's private storage on your phone. Other apps cannot read it. There is no copy on our servers, because we do not run a server that receives your list.

![What RenewRadar does and does not do](fig:a)

## No account, no bank connection

RenewRadar never asks for your name, email address, bank login or card details. There is no sign-up. Nothing is read from your bank or card accounts.

## What goes over the internet

RenewRadar makes only two kinds of network requests:

1. **Service suggestions:** about once a week, the app downloads an updated list of popular service names, categories and usual billing cycles from our website. It is a plain file download that contains nothing about you.
2. **Pro purchase status:** Google Play and RevenueCat, our purchase provider, confirm whether you bought Pro. This uses an anonymous app user ID and the purchase status only.

Your subscriptions, prices, dates and notes are never part of these requests.

## Permissions

RenewRadar asks for one permission: to show notifications, because that is how your reminders reach you. It also uses an internet connection for the two requests above.

![What RenewRadar asks your phone for](fig:b)

RenewRadar does not ask for location, contacts, camera, microphone, photos or files.

## When does your data leave the phone?

Only when you choose. Pro users can export a CSV file of their list. Android's share sheet then asks where to send it: a drive, an email, a messaging app. RenewRadar does not upload it anywhere by itself.

## What happens if you uninstall?

Uninstalling the app deletes its data from your phone. Because there is no cloud copy, the list is gone unless you exported it first. Pro can be restored on any phone with the same Google account.

## Is it safe from other apps on the phone?

Android keeps each app's private storage separate. As long as your phone is protected by a screen lock and kept up to date, other apps cannot read RenewRadar's data.

## Make sure you have the real app

Several apps and websites use similar names. The real RenewRadar is written as one word, published by **Pixel DevCode**, listed on Google Play as **RenewRadar: Subscriptions** with the app ID com.pixeldevcode.renewradar, and has its website at renewradar.pixeldevcode.com. This article and the privacy policy apply only to that app.

## Read the full policy

The details are in our [privacy policy](/privacy.html), written in plain English. If you have questions, write to renewradar@pixeldevcode.com.
