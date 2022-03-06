#include<bits/stdc++.h>
#include<windows.h>
using namespace std;
#define eLaTime double(clock() - ts)/CLOCKS_PER_SEC

int main(const int argc,const char **argv) {
    system("chcp 65001");
    system("cls");

	if(argc<4) {
		cout<<"用法：consolePauser <title> <cwd> <program> [...]"<<endl<<endl;
		cout<<"title   窗口标题"<<endl;
		cout<<"cwd     工作目录"<<endl;
		cout<<"program 程序路径"<<endl;
		cout<<"[...]   参数"<<endl;
		return 3;
	}

	system((string("title ")+argv[1]).c_str());
	SetCurrentDirectory(argv[2]);

	string runcmd = string("\"") + argv[3] + "\"";
	for(int i=4;i<argc;i++) {
		string tmp = argv[i];
		if(tmp.find('&') < tmp.size() || tmp.find(' ') < tmp.size()) {
			if(tmp[tmp.length()-1]=='\\') tmp+="\\";
			runcmd += string(" \"") + tmp + "\"";
		}
		else {
			runcmd += string(" ") + tmp;
		}
	}

	int ts = clock();
	int stO = system(runcmd.c_str());
	cout<<endl<<"----------------------------------------"<<endl;
	cout<<"程序运行完成，用时 "<<eLaTime<<"s，返回值为 "<<stO<<endl;
	// system("pause");
}
