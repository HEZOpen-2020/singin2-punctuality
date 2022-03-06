#include <string>
#include <iostream>
#include "dbmanip.hh"
using namespace std;

signed main() {
	try {
		Connection conn = Connection("E:\\wamp64\\hosts\\PhpTests\\test-app\\singin2-punctuality\\localData.db", "123");
		string query = "SELECT 学生名称 FROM 上课考勤;";

		Statement st = Statement(conn, query);
		
		cout<<"OK"<<endl;

		while(true) {
			auto s = st.next();
			if(!s) break;
			for(int i=0; i<(s->size()); i++) {
				cout<<(s->get<string>(i))<<',';
			}
			cout<<endl;
		}
	} catch(const ErrorOpeningDatabase &e) {
		cout<<"FAIL"<<endl;
		cout<<e.what()<<endl;
	} catch(const PrepareError &e) {
		cout<<"FAIL"<<endl;
		cout<<e.what()<<endl;
	}

	return 0;
}
