#include "multiplier.h"
namespace F_stl {
    #ifndef _FSTL_LIB_LCT
    #define _FSTL_LIB_LCT

    //LCT类
    //  模板：T = 权值类型，C = 叠加器
    //  叠加器调用方法：[C] multiplier(a,b) 
    //  叠加器写法参考“异或符号类”或STL的greater 
    //  叠加器需满足交换、结合律 
    template<class T, class C = Tp_xor<T> >
    struct LCT{
    private:
        //叠加器实例 
        C multiplier;
        //节点类（不作为开放函数的返回值或参数） 
        struct Node {
            Node *fa,*ch[2];
            T val,st;
            bool tag;
            int idx;
            Node () {
                fa=ch[0]=ch[1]=NULL;
                tag=0;
                idx=0; 
            }
            Node (int _idx) {
                fa=ch[0]=ch[1]=NULL;
                tag=0;
                idx=_idx;
            }
        };
        
        //数据
        vector<Node*> data; 
        
        //判定是否 不是 一条实链Splay的根
        bool nroot(Node *t) {
            if(!t->fa) return false;
            return (t->fa->ch[0] == t) || (t->fa->ch[1] == t);
        }
        
        //上传信息（st）
        void push_up(Node *x) {
            x->st = x->val;
            if(x->ch[0]) x->st=multiplier(x->st, x->ch[0]->st);
            if(x->ch[1]) x->st=multiplier(x->st, x->ch[1]->st);
        } 
        
        //翻转节点（tag）
        void flip(Node *x) {
            swap(x->ch[0],x->ch[1]);
            x->tag^=1;
        } 
        
        //下传翻转操作
        void push_down(Node *x) {
            if(x->tag) {
                if(x->ch[0]) flip(x->ch[0]);
                if(x->ch[1]) flip(x->ch[1]);
                x->tag=0;
            }
        }
        
        //Splay Zig-step
        void rotate(Node *x) {
            Node *y=x->fa;Node *R=y->fa;
            bool k = y->ch[1]==x;Node *w=x->ch[!k];
            if(nroot(y)) R->ch[R->ch[1]==y]=x;
            x->ch[!k]=y;y->ch[k]=w;
            if(w) w->fa=y;
            y->fa=x;x->fa=R;
            push_up(y);
            //push_up(x);
        }
        
        stack<Node*> splay_st;
        //Splay splay到Splay根节点
        void splay(Node *x) {
            Node *curr=x;
            splay_st.push(curr);
            while(nroot(curr)) {
                splay_st.push(curr=curr->fa);
                //assert(splay_st.size()<=100004);
            }
            while(!splay_st.empty()) push_down(splay_st.top()),splay_st.pop();
            while(nroot(x)) { //Zig, Zig-Zag, Zig-Zig
                Node *y=x->fa,*R=y->fa;
                if(nroot(y))
                    rotate((y->ch[0]==x)^(R->ch[0]==y)?x:y); // Zig / Zag / None
                rotate(x); //Zig
            }
            push_up(x);
        } 
        
        //打通x到根节点的路径 
        void __access(Node *x) {
            for(Node *y=NULL;x;x=(y=x)->fa) {
                splay(x);
                x->ch[1]=y;
                push_up(x);
            }
        }
        
        //改变树的根节点
        void __makeroot(Node *x) {
            __access(x);splay(x);flip(x);
        }
        
        //查找树的根节点
        Node *__findroot(Node *x) {
            __access(x);splay(x);
            while(x->ch[0]) push_down(x),x=x->ch[0];
            splay(x);
            return x;
        }
        
        //提取路径
        void __split(Node *x,Node *y) {
            __makeroot(x);
            __access(y);splay(y);
        }
        
        //连接边
        bool __link(Node *x,Node *y) {
            __makeroot(x);
            //if(__findroot(y)!=x) cerr<<"[Debug] linked"<<endl;
            if(__findroot(y)!=x) {
                x->fa=y;
                return 1;
            }
            return 0;
        } 

        //判定连通性
        bool __isLinked(Node *x,Node *y) {
            __makeroot(x);
            //if(__findroot(y)!=x) cerr<<"[Debug] linked"<<endl;
            if(__findroot(y)!=x) {
                return 1;
            }
            return 0;
        } 
        
        //断开边
        bool __cut(Node *x,Node *y) {
            __makeroot(x);
            if(__findroot(y)==x && y->fa==x && !y->ch[0]) {
                y->fa = x->ch[1] = NULL;
                push_up(x);
                return 1;
            }
            return 0;
        }
        
    public:
        //重定义大小并初始化
        void resize(unsigned sz) {
            for(unsigned i=0;i<data.size();i++) { //清除原有数据 
                delete data[i];
            }
            data.resize(sz);
            for(unsigned i=0;i<sz;i++) data[i] = new Node(i); //创建新节点 
        }
        
        //取大小
        unsigned size() {
            return data.size();
        } 
        
        //打通x到根节点的路径 
        void access(unsigned idx) {
            __access(data[idx]);
        }
        
        //换根
        void makeroot(unsigned idx) {
            __makeroot(data[idx]);
        } 
        
        //查找树的根节点
        unsigned findroot(unsigned idx) {
            return __findroot(data[idx]) -> idx;
        }
        
        //提取路径
        void split(unsigned x,unsigned y) {
            __split(data[x],data[y]);
        }
        
        //连接边（返回值：操作是否有效）
        bool link(unsigned x,unsigned y) {
            return __link(data[x],data[y]);
        }

        //断开边（返回值：操作是否有效）
        bool cut(unsigned x,unsigned y) {
            return __cut(data[x],data[y]);
        } 
        
        //查询单点值（单点权值） 
        T queryV(unsigned idx) {
            return data[idx]->val;
        }
        
        //查询前缀值（multiple值）
        T queryS(unsigned idx) {
            return data[idx]->st; 
        }
        
        //修改单点值
        void update(unsigned idx,T v) {
            splay(data[idx]);data[idx]->val=v;
        } 
        
        //区间查询
        T query(unsigned x,unsigned y) {
            split(x,y);
            return data[y]->st;
        }

        //判定连通性
        bool isLinked(unsigned x,unsigned y) {
            return !__isLinked(data[x],data[y]);
        }
    };

    #endif
};
