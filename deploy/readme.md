# Example ~/.ssh/config entries:

Host example.staging
Hostname staging.example.com
Port 1234
ForwardAgent true
IdentityFile /Users/me/.ssh/id_rsa
UserKnownHostsFile /dev/null
StrictHostKeyChecking no

Host example.production
Hostname example.com
Port 1234
ForwardAgent true
IdentityFile /Users/me/.ssh/id_rsa
UserKnownHostsFile /dev/null
StrictHostKeyChecking no

