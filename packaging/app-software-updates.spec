
Name: app-software-updates
Epoch: 1
Version: 2.4.0
Release: 1%{dist}
Summary: Software Updates
License: GPLv3
Group: ClearOS/Apps
Source: %{name}-%{version}.tar.gz
Buildarch: noarch
Requires: %{name}-core = 1:%{version}-%{release}
Requires: app-base
Requires: app-network

%description
The Software Updates app provides updates for the underlying operating system components.

%package core
Summary: Software Updates - Core
License: LGPLv3
Group: ClearOS/Libraries
Requires: app-base-core
Requires: app-events-core
Requires: app-network-core
Requires: app-tasks-core
Requires: app-dashboard-core => 1:2.1.22

%description core
The Software Updates app provides updates for the underlying operating system components.

This package provides the core API and libraries.

%prep
%setup -q
%build

%install
mkdir -p -m 755 %{buildroot}/usr/clearos/apps/software_updates
cp -r * %{buildroot}/usr/clearos/apps/software_updates/

install -d -m 0755 %{buildroot}/var/clearos/events/software_updates
install -d -m 0755 %{buildroot}/var/clearos/software_updates
install -D -m 0644 packaging/app-software-updates-cache.cron %{buildroot}/etc/cron.d/app-software-updates-cache
install -D -m 0644 packaging/app-software-updates.cron %{buildroot}/etc/cron.d/app-software-updates
install -D -m 0644 packaging/filewatch-software-updates-event.conf %{buildroot}/etc/clearsync.d/filewatch-software-updates-event.conf
install -D -m 0755 packaging/software-updates %{buildroot}/usr/sbin/software-updates
install -D -m 0644 packaging/software_updates.conf %{buildroot}/etc/clearos/software_updates.conf

%post
logger -p local6.notice -t installer 'app-software-updates - installing'

%post core
logger -p local6.notice -t installer 'app-software-updates-core - installing'

if [ $1 -eq 1 ]; then
    [ -x /usr/clearos/apps/software_updates/deploy/install ] && /usr/clearos/apps/software_updates/deploy/install
fi

[ -x /usr/clearos/apps/software_updates/deploy/upgrade ] && /usr/clearos/apps/software_updates/deploy/upgrade

exit 0

%preun
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-software-updates - uninstalling'
fi

%preun core
if [ $1 -eq 0 ]; then
    logger -p local6.notice -t installer 'app-software-updates-core - uninstalling'
    [ -x /usr/clearos/apps/software_updates/deploy/uninstall ] && /usr/clearos/apps/software_updates/deploy/uninstall
fi

exit 0

%files
%defattr(-,root,root)
/usr/clearos/apps/software_updates/controllers
/usr/clearos/apps/software_updates/htdocs
/usr/clearos/apps/software_updates/views

%files core
%defattr(-,root,root)
%exclude /usr/clearos/apps/software_updates/packaging
%exclude /usr/clearos/apps/software_updates/unify.json
%dir /usr/clearos/apps/software_updates
%dir /var/clearos/events/software_updates
%dir /var/clearos/software_updates
/usr/clearos/apps/software_updates/deploy
/usr/clearos/apps/software_updates/language
/usr/clearos/apps/software_updates/libraries
/etc/cron.d/app-software-updates-cache
%config(noreplace) /etc/cron.d/app-software-updates
/etc/clearsync.d/filewatch-software-updates-event.conf
/usr/sbin/software-updates
%config(noreplace) /etc/clearos/software_updates.conf
