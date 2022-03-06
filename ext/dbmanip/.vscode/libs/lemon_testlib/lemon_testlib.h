#ifndef _AKOI_LEMON_TESTLIB
#define _AKOI_LEMON_TESTLIB

#include<stdio.h>
#include<iostream>
#include<fstream>
#include<cmath>
#include<string>
#include<cctype>
#include<sstream>
#include<vector>

// Lemon Testlib
// 需要 C++11
namespace ltl {
    // 评测状态Wrong Answer
    const int _wa = 0;

    // 评测状态Accepted
    const int _ok = 1;
    
    // 评测状态Invalid checker
    const int _err = -1; 

    // 分数输出文件
    FILE *fscore = NULL;

    // 信息输出文件
    FILE *freport = NULL;

    // 测试点总分
    int testscore = 0;

    // 给出WA或AC状态，输出信息并退出
    template<class ...Tp>
    void quitf(int st,const char *str,Tp ...arg);

    // 给出部分分，输出信息并退出
    template<class ...Tp>
    void quitp(double st,const char *str,Tp ...arg);

    // 辅助函数：将不可见字符转换为可以阅读的形式
    std::string __convChar(char c) {
        if(c >= 33 && c <= 127) return std::string("") + c;
        if(c == 32) return "space";
        return std::string("ASCII") + std::to_string(int(c));
    }

    // 判断是否整数
    bool __isInterger(std::string s) {
        if(s[0] == '-') s = s.substr(1);
        else if(s[0] == '+') s = s.substr(1); 
        for(int i=0;i<(int)s.size();i++) {
            if(!isdigit(s[i])) return false;
        }
        return true;
    }
    
    // 判断是否实数，并返回精度值
    int __isReal(std::string s) {
        if(s[0] == '-') s = s.substr(1);
        else if(s[0] == '+') s = s.substr(1);
        int cnt = 0;
        for(int i=0;i<(int)s.size();i++) {
            if(!isdigit(s[i]) && s[i] != '.') return false;
            if(s[i] == '.') cnt++;
        }
        if(s.empty()) return -1;
        if(cnt == 0) return 0;
        if(cnt == 1) {
            if(s[s.size()-1] == '.') return -1;
            return s.size() - s.find('.') - 1;
        }
        return -1;
    }
    
    // 输入文件接口
    struct testlibInput : std::ifstream {
        std::string fname;
        // 读入一个字符（行为同getchar）
        char readChar() {
            char ret = (*this).get();
            if(ret == EOF) {
                quitf(_err,"期望读取一个字符，然而读到EOF（%s）",fname.c_str());
            }
            return ret;
        }
        // 将光标移到指定字符之后
        char readChar(char c) {
            char ret = (*this).get();
            while(ret != c && ret != EOF) ret = (*this).get();
            if(ret == EOF) {
                quitf(_err,"期望找到字符%s，然而读到EOF（%s）",__convChar(c).c_str(),fname.c_str());
            }
            return ret;
        }
        // 将光标移到空格之后
        void readSpace() {
            char ret = (*this).get();
            while(!isspace(ret) && ret!=EOF) ret = (*this).get();
            if(ret == EOF) {
                quitf(_err,"期望找到空白字符，然而读到EOF（%s）",fname.c_str());
            }
            while(isspace(ret) && ret!=EOF) {
                ret = (*this).peek();
                if(!isspace(ret) || ret==EOF) return;
                (*this).get();
            }
        }
        // 辅助函数，读入一个连续而不含空白字符的字符串
        std::string __readToken() {
            char c = (*this).get();
            while(isspace(c)) {
                c = (*this).get();
                if(c == EOF) break;
            }
            if(c==EOF) {
                return "";
            }
            std::string ret = "";
            while(!isspace(c) && c!=EOF) {
                ret += c;
                c = (*this).peek();
                if(c==EOF || isspace(c)) break;
                c = (*this).get();
            }
            return ret;
        }
        // 读入一个连续而不含空白字符的字符串
        std::string readToken() {
            std::string ret = __readToken();
            if(ret.empty()) {
                quitf(_err,"期望读到一个字符串，然而读到EOF（%s）",fname.c_str());
            }
            return ret;
        }
        // 辅助函数：读取一个整数
        template<class T>
        T __readLong(
            int digits = 64,
            unsigned limitlen = 19,
            std::string llimit = "9223372036854775808",
            std::string rlimit = "9223372036854775807"
        ) {
            std::string str = __readToken();
            if(str.empty()) {
                quitf(_err,"期望读取一个%d位整数，然而读到EOF（%s）",digits,fname.c_str());
            }
            if(!__isInterger(str)) {
                quitf(_err,"期望读到一个%d位整数，然而读到%s（%s）",digits,str.c_str(),fname.c_str());
            }
            T flag = 1;
            std::string orig = str;
            if(str[0] == '-') {
                flag = -1;
                str = str.substr(1);
            }
            else if(str[0] == '+') {
                str = str.substr(1);
            } 
            while(str[0] == '0') str = str.substr(1);
            if(str.size() > limitlen) {
                quitf(_err,"期望读到一个%d位整数，然而读到%s（%s）",digits,orig.c_str(),fname.c_str());
            }
            if(flag == -1) {
                if(str.size() == limitlen && str > llimit) {
                    quitf(_err,"期望读到一个%d位整数，然而读到%s（%s）",digits,orig.c_str(),fname.c_str());
                }
            }
            else {
                if(str.size() == limitlen && str > rlimit) {
                    quitf(_err,"期望读到一个%d位整数，然而读到%s（%s）",digits,orig.c_str(),fname.c_str());
                }
            }
            T ret = 0;
            for(unsigned i=0;i<str.size();i++) {
                ret = (ret * 10) + flag*(str[i] - '0');
            }
            return ret;
        }
        // 读取一个64位整数（可限定范围）
        long long readLong() {
            return __readLong<long long>();
        }
        long long readLong(long long L,long long R) {
            long long ret = readLong();
            if(ret < L || ret > R) {
                quitf(_err,"读到64位整数%lld，但是不在限定范围[%lld,%lld]内（%s）",ret,L,R,fname.c_str());
            }
            return ret;
        }
        // 读取一个32位整数（可限定范围）
        int readInt() {
            return __readLong<int>(32,10,"2147483648","2147483647");
        }
        int readInt(int L,int R) {
            int ret = readInt();
            if(ret < L || ret > R) {
                quitf(_err,"读到32位整数%d，但是不在限定范围[%d,%d]内（%s）",ret,L,R,fname.c_str());
            }
            return ret;
        }
        // 辅助函数：读入实数
        double __readReal(
            int minPrec = -1,
            int maxPrec = 2147483647
        ) {
            std::string str = __readToken();
            if(str.empty()) {
                quitf(_err,"期望读到一个实数，然而读取到EOF（%s）",fname.c_str());
            }
            int prec = __isReal(str);
            if(prec == -1) {
                quitf(_err,"期望读到一个实数，然而读取到%s（%s）",str.c_str(),fname.c_str());
            } 
            if(prec < minPrec || prec > maxPrec) {
                quitf(_err,"期望读到一个精度范围[%d,%d]的实数，然而读取到%s（%s）",minPrec,maxPrec,str.c_str(),fname.c_str());
            }
            std::string orig = str;
            std::string sign = "";
            if(str[0] == '-') {
                sign = "-";
                str = str.substr(1);
            }
            else if(str[0] == '+') {
                // sign = "+"; 
                str = str.substr(1);
            }
            bool hasDot = false;
            for(unsigned i=0;i<str.size();i++) {
                if(str[i] == '.') hasDot = true;
            }
            while(hasDot && str[str.size()-1] == '0') str = str.substr(0,str.size()-1);
            if(str[str.size()-1] == '.')  str = str.substr(0,str.size()-1);
            while(str[0] == '0') str = str.substr(1);
            if(str.empty() || str[0] == '.') str = std::string("0") + str;
            str = sign + str;
            
            std::string tmp;
            std::stringstream ss1;
            ss1<<str;
            double ret;
            ss1>>ret;
            std::stringstream ss2;
            ss2<<ret;
            ss2>>tmp;
            
            // int shf = 0;
            // for(unsigned i=0;i<5;i++) {
            //     if(i>=str.size() || i>=tmp.size()) break;
            //     if(str[i] != tmp[i+shf]) {
            //         if(tmp.find('e') && tmp[i+shf]=='.' && shf==0) {
            //             shf = 1;
            //             i--;
            //             continue;
            //         }
            //         quitf(_err,"期望读到一个实数，然而读取到%s，格式化后变成%s，实际实数是%s（%s）",orig.c_str(),str.c_str(),tmp.c_str(),fname.c_str());
            //     }
            // }
            
            return ret;
        }
        // 读取一个实数（可限定范围）
        // 注意此处不允许读入inf，NaN，-1.#IND00，0.NAN000等数。如果出现这类数，将会判定选手WA
        double readReal() {
            return __readReal();
        }
        double readReal(double L,double R) {
            double ret = readReal();
            if(ret < L || ret > R) {
                quitf(_err,"读取到实数%lf，然而不在限定范围[%lf,%lf]内（%s）",ret,L,R,fname.c_str());
            }
            return ret;
        }
        // 读取有限定范围、有精度范围的实数
        double readStrictReal(double L,double R,int minPrec,int maxPrec) {
            double ret = __readReal(minPrec,maxPrec);
            if(ret < L || ret > R) {
                quitf(_err,"读取到实数%lf，然而不在限定范围[%lf,%lf]内（%s）",ret,L,R,fname.c_str());
            }
            return ret;
        }
        // 读入一行，光标移到下一行开头
        std::string readLine() {
            std::string ret = "";
            char c = (*this).get();
            if(c == EOF) {
                return ret;
            }
            while(c != '\n' && c!='\r') {
                ret += c;
                c = (*this).get();
                if(c == EOF) return ret;
            }
            while(c == '\n' || c=='\r') {
                c = (*this).peek();
                if(c != '\n' && c!='\r') break;
                (*this).get();
            }
            return ret;
        }
        // 读入一行，光标移到下一行开头
        std::string readString() {
            return readLine();
        }
        // 读入一个换行符或文件末尾
        void readEoln() {
            char c = (*this).get();
            if(c == EOF) {
                return;
            }
            while(c != '\n' && c!='\r') {
                c = (*this).get();
                if(c == EOF) return;
            }
            while(c == '\n' || c=='\r') {
                c = (*this).peek();
                if(c != '\n' && c!='\r') break;
                (*this).get();
            }
        } 
        // 强制读到EOF
        bool readEof() {
            bool ret = 0;
            char c = (*this).get();
            while(c != EOF) {
                if(!isspace(c)) ret = 1;
                c = (*this).get();
            }
            return ret;
        }
        // 是否到达EOF
        bool eof() {
            return (*this).peek() == EOF;
        }
    }inf,ouf,ans,source;
    
    // 给出WA或AC状态，输出信息并退出
    template<class ...Tp>
    void quitf(int st,const char *str,Tp ...arg) {
        if(st==1 && ouf.readEof()) {
            quitf(-1,"选手输出中有多余的内容");
        }
        if(st==-1) st = 0;
        fprintf(fscore,"%d",st*testscore);
        fprintf(freport,str,arg...);
        if(st == 1) printf("ok ");
        else printf("wrong answer ");
        printf(str,arg...);
        exit(0);
    }

    // 给出部分分，输出信息并退出
    template<class ...Tp>
    void quitp(double st,const char *str,Tp ...arg) {
        if(st>0 && ouf.readEof()) {
            quitf(-1,"选手输出中有多余的内容");
        }
        fprintf(fscore,"%d",(int)round(st*testscore));
        fprintf(freport,str,arg...);
        printf("points %d ",(int)round(st*testscore));
        printf(str,arg...);
        exit(0);
    }

    // 输入参数，初始化Testlib
    void registerTestlibCmd(int argc,char **argv) {
        if(argc < 6) exit(27800);
        inf.open(argv[1]);
        inf.fname = "输入数据";
        ouf.open(argv[2]);
        ouf.fname = "选手输出";
        ans.open(argv[3]);
        ans.fname = "输出数据";
        testscore = atoi(argv[4]);
        fscore = fopen(argv[5],"w");
        freport = fopen(argv[6],"w");
    }

    // 给出可选选手程序列表，找到选手程序。随后可以从全局变量source读入选手程序
    // 没有必要时无需调用。
    void registerSourceReader(int argc,char **argv,std::initializer_list<std::string> lst) {
        std::string dir = argv[5];
        unsigned pos = dir.find_last_of('\\');
        unsigned tmp = dir.find_last_of('/');
        // quitf(_err,"测试：%u %u",pos,tmp);
        if(pos >= dir.length() && tmp >= dir.length()) {
            quitf(_err,"评测工作目录疑似不合法");
        }
        if(pos >= dir.length() || (tmp<dir.length() && tmp > pos)) pos = tmp;
        dir = dir.substr(0,pos+1);
        for(std::initializer_list<std::string>::iterator it = lst.begin();it != lst.end();++it) {
            source.open((dir + (*it)).c_str());
            // quitf(_err,"%s",(dir + (*it)).c_str());
            // return;
            if(!source.fail()) return;
        }
        quitf(_err,"无法找到选手的源代码（很可能是校验器出现问题）");
    }
};

#endif
