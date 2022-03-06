#include <string>
#include <iostream>
#include "dbmanip.hh"
#include "base64.hh"
using namespace std;

signed main(const int argc, const char **argv) {
	if(argc < 4 || !is_base64_str(argv[1]) || !is_base64_str(argv[2]) || !is_base64_str(argv[3])) {
		cout << "Usage: dbquery <db_path:base64> <key:base64> <query:base64>" << endl;
		return 3;
	}

	try {
		Connection conn = Connection(base64_decode(argv[1]).c_str(), base64_decode(argv[2]).c_str());
		string query = base64_decode(argv[3]);

		Statement st = Statement(conn, query);
		
		cout<<"OK"<<endl;

		while(true) {
			auto s = st.next();
			if(!s) break;
			for(int i=0; i<(s->size()); i++) {
				if(i != 0) cout<<",";
				cout<<base64_encode(s->get<string>(i));
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
