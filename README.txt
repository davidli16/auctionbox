I WOULD LIKE TO ENTER THE AUCTIONBASE CONTEST

-------------

#  Ability to manually change the "current time."

There is a persistent bar at the bottom of the screen that allows the user to change the time at any time. It is like a time traveling control of the website.

# Ability for auction users to enter bids on open auctions.

The user can enter bids by going the the auction page for an item. This can be reached by going through the "Browse" option or the "Search" option to find a product. Once on the product page, there will be a "Place Bid" option only if the auction is still open. If the auction is closed, it will show the winner (or possibly no one if the auction closed without any bids).

# Automatic auction closing. An auction is "open" after its start time and "closed" when its end time is past or its buy price is reached. Your design may be such that an auction closes implicitly with high enough bids or a time update, or you may have chosen to represent open/closed status with an explicit data field.

The Browse window will automatically keep track of the status on an auction given the current time (so you can go back and forth a couple days and see auctions open and close). If an auction is open, the status will show the remaining time on the auction. 

# Ability to see the winner of a closed auction.

I mentioned this above. On the product page, it will show the winner of the auction if the auction is closed.

# Ability to browse auctions of interest based on some simple input choices such as category, price, and open/closed status.

There is a filter bar at the top of the Browse view that allows you to filter the results by category, min/max price, and status (as well as page).

-------------

When browsing auctions, a user can perform a search that will go through all the name and descriptions fields for the currently existing auctions.

Additionally, as mentioned above, the user can select among categories, price, open / closed status, and pages. 

If the user clicks the column heading in the browse window, the table is sorted by that column (does not allow you to toggle =[).

Clicking the Categories under each auction name will also filter by category. Clicking the Seller name allows you to see the seller profile. 

Finally, you can configure the current time at the bottom of the screen to see the auctions change instantaneously. (Performance is a bit slow unfortunately)

-------------

I think there are a lot of random little things so feel free to take a look. I guess I will just summarize the overall views and the interesting things about those views that aren't part of the original project requirements.

1) User Profile Screen: Whenever there is a username (seller or bidder), it will be a link to that user's profile page which will show the recent bids, recent auctions, and profile for that user.

2) Browse View: Sort by column, time until auction ends, filter bar / pagination.

3) Search View: Allows a simple, minimalistic way to access the database.

4) Create View: Allows you to create a new auction. You may have up to 4 Categories. Will handle errors relatively gracefully (if you click the error box or wait 8 seconds, it will return you to the create screen so you can fix the problems). End date defaults to 1 day after the current date.

5) User "authentication": Adds new users to the database if they are not already in the database. Also allows you to logout and change your username.