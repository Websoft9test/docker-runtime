#!/usr/bin/env perl
#
#  htpasswd.pl
#  
# Copyright (c) 2013, Gelu Lupas <gelu@devnull.ro>
#
# Permission to use, copy, modify, and/or distribute this software for any
# purpose with or without fee is hereby granted, provided that the above
# copyright notice and this permission notice appear in all copies.
#
# THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
# WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
# MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
# ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
# WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
# ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
# OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE

use strict;
use warnings;

use Getopt::Std;
use Authen::Htpasswd;

$Getopt::Std::STANDARD_HELP_VERSION = 1;
my %arg;
my %opt = (
	b => 0,
	c => 0,
	n => 0,
	m => 0,
	d => 0,
	p => 0,
	s => 0,
	D => 0,
);
getopts('bcnmdpsD', \%opt);

sub HELP_MESSAGE {
	print <<END;
Usage:

	htpasswd [-cmdpsD] passwordfile username
	htpasswd -b[cmdpsD] passwordfile username password

	htpasswd -n[mdps] username
	htpasswd -nb[mdps] username password

-c  Create a new file.
-n  Don't update file; display results on stdout.
-m  Force MD5 encryption of the password.
-d  Force CRYPT encryption of the password (default).
-p  Do not encrypt the password (plaintext).
-s  Force SHA encryption of the password.
-b  Batch mode; use the password from the command line.
-D  Delete the specified user.
END
}

sub VERSION_MESSAGE {}

sub read_pw {
	# read password from STDIN
	require Term::ReadKey;
	print "New password: ";
	Term::ReadKey::ReadMode('noecho');
	chomp(my $pw1 = <STDIN>);
	print "\nRe-type new password: ";
	chomp(my $pw2 = <STDIN>);
	Term::ReadKey::ReadMode('restore');
	print "\n";
	if ($pw1 eq $pw2) {
		return $pw1;
	}
	# TODO password length/strength checks?
	else {
		die "Password verification error";
	}
}

sub get_pw_hash {
	# password hasing
	my ($opt, $arg) = @_;
	
	my $pwh;
	if ($opt->{m}) {
		require Crypt::PasswdMD5;
		$pwh = Crypt::PasswdMD5::apache_md5_crypt($arg->{pw});
	}
	elsif ($opt->{s}) {
		require Digest::SHA1;
		$pwh = '{SHA}'.Digest::SHA1::sha1_base64($arg->{pw}).'=';
	}
	elsif ($opt->{p}) {
		$pwh = $arg->{pw};
	}
	else {
		my $salt = 
			join '', ('.', '/', 0..9, 'A'..'Z', 'a'..'z')[rand 64, rand 64];
		$pwh = crypt($arg->{pw}, $salt);		
	}
	return $pwh;
}

sub get_pw_file {
	# password hasing
	my ($opt, $arg) = @_;
	
	my $ret;
	my $htp;
	if($opt->{m}) {
		$htp = new Authen::Htpasswd($arg->{pwf}, {encrypt_hash => 'md5'});
		$ret = $htp->update_user($arg->{usr}, $arg->{pw});
	}
	elsif ($opt->{s}) {
		$htp = new Authen::Htpasswd($arg->{pwf}, {encrypt_hash => 'sha1'});
		$ret = $htp->update_user($arg->{usr}, $arg->{pw});
	}
	elsif ($opt->{p}) {
		$htp = new Authen::Htpasswd($arg->{pwf}, {encrypt_hash => 'plain'});
		$ret = $htp->update_user($arg->{usr}, $arg->{pw});
	}
	else {
		$htp = new Authen::Htpasswd($arg->{pwf}, {encrypt_hash => 'crypt'});
		$ret = $htp->update_user($arg->{usr}, $arg->{pw});
	}
	return $ret;
}

if ($opt{n}) {
	# output only mode; arguments
	%arg = (
		usr => $ARGV[0],
		pw => $ARGV[1],
	);
	if(!$arg{usr}) {
		HELP_MESSAGE;
		exit;
	}
	# batch mode
	if ($opt{b}) {
		if ($arg{pw}) {
			print $arg{usr}.':'.get_pw_hash(\%opt, \%arg)."\n";
		}
		else {
			die "No password specified";
		}
	}
	# interactive mode
	else {
		$arg{pw} = read_pw();
		print $arg{usr}.':'.get_pw_hash(\%opt, \%arg)."\n";
	}
}
else {
	# write to file mode; arguments
	%arg = (
		pwf => $ARGV[0],
		usr => $ARGV[1],
		pw => $ARGV[2],
	);
	unless($arg{pwf} && $arg{usr}) {
		HELP_MESSAGE;
		exit;
	}
	# delete user
	if($opt{D}) {
		if (-f $arg{pwf}) {
			my $htp = new Authen::Htpasswd($arg{pwf});
			if($htp->delete_user($arg{usr})) {
				print "User \'$arg{usr}\' successfully deleted\n";
			}
			unlink($arg{pwf}) if -z $arg{pwf};
			exit;
		}
		else {
			die "Password file \'$arg{pwf}\' does not exist";
		}
	}
	# batch mode
	if ($opt{b}) {
		if($arg{pw}) {
			if(-f $arg{pwf}) {
				get_pw_file(\%opt, \%arg);
			}
			elsif($opt{c}) {
				my $fh;
				open($fh, ">", "$arg{pwf}")
					or die "Cannot open $arg{pwf} for writing: $!";
				close $fh;
				get_pw_file(\%opt, \%arg);
			}
			else {
				die "Password file \'$arg{pwf}\' does not exist";
			}
		}
		else {
			die "No password specified\n";
		}
	}
	# interactive mode
	else {
		$arg{pw} = read_pw();
		if(-f $arg{pwf}) {
			get_pw_file(\%opt, \%arg);
		}
		elsif($opt{c}) {
			my $fh;
			open($fh, ">", "$arg{pwf}")
				or die "Cannot open $arg{pwf} for writing: $!";
			close $fh;
			get_pw_file(\%opt, \%arg);
		}
		else {
			die "Password file \'$arg{pwf}\' does not exist";
		}
	}
}

=pod

=head4 Usage:

	htpasswd [-cmdpsD] passwordfile username
	htpasswd -b[cmdpsD] passwordfile username password

	htpasswd -n[mdps] username
	htpasswd -nb[mdps] username password

 -c  Create a new file.
 -n  Don't update file; display results on stdout.
 -m  Force MD5 encryption of the password.
 -d  Force CRYPT encryption of the password (default).
 -p  Do not encrypt the password (plaintext).
 -s  Force SHA encryption of the password.
 -b  Batch mode; use the password from the command line.
 -D  Delete the specified user.

=cut
