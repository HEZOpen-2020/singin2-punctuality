# 1 "<stdin>"
# 1 "<built-in>"
# 1 "<command-line>"
# 1 "<stdin>"
# 1 "valcomp.h" 1

namespace F_stl {



    template<class T>
    struct ValComp_less {
        inline bool operator() (const T &a,const T &b) {return a<b;}
    };


    template<class T>
    struct ValComp_greater {
        inline bool operator() (const T &a,const T &b) {return a>b;}
    };

}
# 2 "<stdin>" 2

namespace F_stl {




    template<class T,class CT = ValComp_less<T> >
    struct LeftistTree {
    private:

        struct Node {
            int dist,idx;
            T val;
            Node *L,*R;
            Node *rtp;
            Node() {L=R=NULL;dist=1;idx=0;rtp=NULL;}
            Node(T _val,int _idx) {L=R=NULL;dist=1;val=_val;idx=_idx;rtp=NULL;}
        };


        CT comp;


        vector<Node*> data;


        inline void __clear(Node *t) {
            if(!t) return;
            try{
                __clear(t->L);__clear(t->R);
                delete t;
            }catch(exception e) {}
        }


        Node *__merge(Node *x,Node *y) {
            if(x==NULL || y==NULL) {
                return x?x:y;
            }
            if(comp(x->val,y->val)) swap(x,y);
            x->R = __merge(x->R,y);
            if((x->L?(x->L->dist):0) < (x->R?(x->R->dist):0)) {
                swap(x->L,x->R);
            }
            x->dist = (x->R?(x->R->dist):0)+1;
            return x;
        }

        Node *__rt(Node *t) {
            if(t->rtp == t) return t;
            return t->rtp=__rt(t->rtp);
        }

    public:

        bool fastmode;


        LeftistTree(){}


        inline void clear() {
            for(unsigned i=0;i<data.size();i++) __clear(data[i]);
            data.resize(0);
        }


        inline int create(T val) {
            data.push_back(new Node(val,data.size()));
            int ret = data.size()-1;
            data[ret]->rtp = data[ret];
            return ret;
        }


        inline int rt(unsigned idx) {
            return __rt(data[idx])->idx;
        }


        inline bool merge(unsigned x,unsigned y) {
            Node *a = __rt(data[x]),*b=__rt(data[y]);
            if(a==b) return 0;
            a->rtp = b->rtp = __merge(a,b);return 1;
        }


        inline T top(unsigned idx) {
            return __rt(data[idx])->val;
        }


        inline T pop(unsigned idx) {
            Node *curr = __rt(data[idx]);
            T ret = curr->val;
            Node *h = __merge(curr->L,curr->R);
            curr->rtp = h;
            if(curr->L) curr->L->rtp=h;
            if(curr->R) curr->R->rtp=h;
            curr->L=curr->R=NULL;
            curr->dist=0;
            if(!fastmode) data[curr->idx] = new Node(curr->val,curr->idx);
            return ret;
        }


        inline void update(unsigned idx,T val) {
            data[idx]->val = val;
        }


        inline T query(unsigned idx) {
            return data[idx]->val;
        }


        inline int hh(unsigned idx) {
            return data[idx]->dist;
        }
    };


};
