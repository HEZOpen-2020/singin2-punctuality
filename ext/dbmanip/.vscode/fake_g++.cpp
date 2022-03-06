// �������г��� 
#include<bits/stdc++.h>
#include<windows.h>
using namespace std;
#define eLaTime double(clock() - ts)/CLOCKS_PER_SEC

// C:\Users\yezhi\!�ҵ��ļ�\I AK IOI\.vscode\libs\testlib-0.9.12 
int main(const int argc,const char *argv[]) {
	// system("chcp 936");
	// system("cls");
	
	if(argc<4) {
		cout<<"����ֻ������"<<argc<<"������"<<endl;
		// system("pause");
		return 3;
	}
	// cerr<<"iakioi"<<endl;
//	else if(argc>4) {
//		string re_cmd = "start \"Compile&Run C++\"";
//		re_cmd += string(" \"") + argv[0] + "\"";
//		re_cmd += string(" \"") + argv[1] + "\"";
//		re_cmd += string(" \"") + argv[2] + "\\\"";
//		re_cmd += string(" \"") + argv[3] + "\"";
//		cout<<"�����������"<<re_cmd<<endl;
//		system(re_cmd.c_str());
//		return 0;
//	}
	
	// command args
	string codefile = argv[1];
	string configpath = argv[2];
	string cwd = argv[3];
	bool quickmode=false;
	bool specialjudge=false; // �Ƿ��ڵ���Special Judge���� 
	bool norun=false;
	bool fff=false;
	
	// !
	if(codefile!="" && (codefile[0]=='!' || codefile[0]=='?')) {
		if(codefile[0]=='?') fff=true;
		quickmode=true;
		codefile=codefile.substr(1);
		if(codefile!="" && codefile[0]=='!') {
			norun=true;
			codefile=codefile.substr(1);
		}
	}
	
	// >
	ifstream cf(codefile.c_str());
	string firstline;
	getline(cf,firstline);
	unsigned checkeridx=firstline.find("!This is a checker program");
	if(checkeridx < firstline.size() && checkeridx > 0) {
		specialjudge=true;
	}
	
	// Extra config
	//string compiler_path="C:\\MinGW\\bin";
	string compiler_path="D:\\MinGW\\bin";
	//string compiler_path="uakioi";
	string compiler="g++.exe";
	string additionfile = configpath+"extra-option.txt";
	string A_origin = "no";
	string A_publish = "no";
	
	// Read options
	ifstream F_additionfile(additionfile.c_str());
	string opt;
	getline(F_additionfile,opt);
	
	// Ask questions
	if(!norun) cout<<"���������ļ���"<<codefile<<endl; 
	if(specialjudge && !norun) cout<<"* ����һ��Special Judge��checker����"<<endl;
	if(!norun) cout<<"�����ԭ·��(yes/no)��[no] ";
	if(!quickmode) getline(cin,A_origin);
	if(!norun) cout<<"�����汾(yes/no)�� [no] ";
	if(!quickmode) getline(cin,A_publish);
	if(fff) A_origin = "yes";
	
	// Get output path
	string output="";
	___GetOriginPath: {
		int idx = codefile.length()-1;
		for(;idx>=0;idx--) {
			if(codefile[idx]=='.') break;
			if(codefile[idx]=='/' || codefile[idx]=='\\') {
				idx=-1;break;
			}
			if(idx<0) output = codefile+".exe";
			else      output = codefile.substr(0,idx-1)+".exe";
		}
	}
	if(A_origin!="yes") {
		output = configpath + "debug-cpp\\cdebug.exe";
	}
	if(!norun) cout<<endl<<"���·����"<<output<<endl;
	
	// Compiler command
	SetCurrentDirectory(compiler_path.c_str());
	string compile_cmd = string("")+compiler+"";
	compile_cmd += string(" \"") + codefile + "\"";
	compile_cmd += string(" \"") + configpath + "libs\\link\\sqlite3mc_x64.dll" + "\"";
	compile_cmd += " -static ";
	compile_cmd += " -o ";
	compile_cmd += string("\"") + output + "\"";
	compile_cmd += string(" ") + opt;
	compile_cmd += " -DOFFLINE_JUDGE";
	if(A_publish!="yes") {
		compile_cmd += " -DDEBUG -g";
	}
	compile_cmd += string("") + " -I \"" + configpath + "libs\""; 
	compile_cmd += string("") + " -I \"" + configpath + "libs\\testlib-0.9.12\"";
	compile_cmd += string("") + " -I \"" + configpath + "libs\\lemon_testlib\""; 
	if(!norun) cout<<endl<<"��������> "<<compile_cmd<<endl;
	if(!norun) cout<<endl;
	
	if(!quickmode) system("pause");
	// system("cls");
	
	if(!norun) cout<<endl<<"----------------------------------------"<<endl;
	
	// Compile the code
	if(!norun) cout<<"���ڵȴ���ɱ���..."<<endl;
	int ts = clock();
	int stO = system(compile_cmd.c_str());
	cout<<endl<<"----------------------------------------"<<endl;
	if(stO > 0) {
		cout<<"����ʧ�ܣ���ʱ"<<eLaTime<<"s������ֵΪ"<<stO<<endl;
		// if(!norun) system("pause");
		return stO;
	}
	cout<<"����ɹ�����ʱ"<<eLaTime<<"s"<<endl;
	 
	if(norun) {
		// system("pause");
		return 0;
	}
	
	// Run the program
	SetCurrentDirectory(cwd.c_str());
	string runcmd = string("start /wait \"����C++����\" \"") + configpath + "consolePauser.exe\" ����C++���� \"" + cwd + "\" ";
	runcmd += string("\"") + output + "\"";
	if(specialjudge) runcmd += " in.txt out.txt ans.txt";
	// runcmd = string("console");
	system(runcmd.c_str());
	
	
	// if(!norun) system("pause");
	
	return 0;
}


