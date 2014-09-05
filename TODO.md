Fixes:
======

1. Nack messages don't get back in the queue because the requeue flag is not set.
2. Implement the possibility to attach an unacknowledged adapter that will automaticly
   take care of the messages which cannot be processed. At the moment there is still a
   risk of losing messages that are unacknowledged.
