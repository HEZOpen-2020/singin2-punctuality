namespace F_stl {
    template<class T>
    struct Tp_xor {
        T operator() (const T &a,const T &b) const {
            return a^b;
        }
    };

    template<class T>
    struct Tp_sum {
        T operator() (const T &a,const T &b) const {
            return a+b;
        }
    };


};
namespace F_stl {
    template<class T, class C = Tp_xor<T> >
    struct LCT {
    private:
        C multiplier;

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

        vector<Node*> data;

        bool nroot(Node *t) {
            if(!t->fa) return false;
            return (t->fa->ch[0] == t) || (t->fa->ch[1] == t);
        }

        void push_up(Node *x) {
            x->st = x->val;
            if(x->ch[0]) x->st=multiplier(x->st, x->ch[0]->st);
            if(x->ch[1]) x->st=multiplier(x->st, x->ch[1]->st);
        }

        void flip(Node *x) {
            swap(x->ch[0],x->ch[1]);
            x->tag^=1;
        }

        void push_down(Node *x) {
            if(x->tag) {
                if(x->ch[0]) flip(x->ch[0]);
                if(x->ch[1]) flip(x->ch[1]);
                x->tag=0;
            }
        }

        void rotate(Node *x) {
            Node *y=x->fa;
            Node *R=y->fa;
            bool k = y->ch[1]==x;
            Node *w=x->ch[!k];
            if(nroot(y)) R->ch[R->ch[1]==y]=x;
            x->ch[!k]=y;
            y->ch[k]=w;
            if(w) w->fa=y;
            y->fa=x;
            x->fa=R;
            push_up(y);

        }

        stack<Node*> splay_st;
        void splay(Node *x) {
            Node *curr=x;
            splay_st.push(curr);
            while(nroot(curr)) {
                splay_st.push(curr=curr->fa);

            }
            while(!splay_st.empty()) push_down(splay_st.top()),splay_st.pop();
            while(nroot(x)) {
                Node *y=x->fa,*R=y->fa;
                if(nroot(y))
                    rotate((y->ch[0]==x)^(R->ch[0]==y)?x:y);
                rotate(x);
            }
            push_up(x);
        }

        void __access(Node *x) {
            for(Node *y=NULL; x; x=(y=x)->fa) {
                splay(x);
                x->ch[1]=y;
                push_up(x);
            }
        }

        void __makeroot(Node *x) {
            __access(x);
            splay(x);
            flip(x);
        }

        Node *__findroot(Node *x) {
            __access(x);
            splay(x);
            while(x->ch[0]) push_down(x),x=x->ch[0];
            splay(x);
            return x;
        }

        void __split(Node *x,Node *y) {
            __makeroot(x);
            __access(y);
            splay(y);
        }

        bool __link(Node *x,Node *y) {
            __makeroot(x);

            if(__findroot(y)!=x) {
                x->fa=y;
                return 1;
            }
            return 0;
        }

        bool __isLinked(Node *x,Node *y) {
            __makeroot(x);

            if(__findroot(y)!=x) {
                return 1;
            }
            return 0;
        }

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
        void resize(unsigned sz) {
            for(unsigned i=0; i<data.size(); i++) {
                delete data[i];
            }
            data.resize(sz);
            for(unsigned i=0; i<sz; i++) data[i] = new Node(i);
        }

        unsigned size() {
            return data.size();
        }

        void access(unsigned idx) {
            __access(data[idx]);
        }

        void makeroot(unsigned idx) {
            __makeroot(data[idx]);
        }

        unsigned findroot(unsigned idx) {
            return __findroot(data[idx]) -> idx;
        }

        void split(unsigned x,unsigned y) {
            __split(data[x],data[y]);
        }

        bool link(unsigned x,unsigned y) {
            return __link(data[x],data[y]);
        }

        bool cut(unsigned x,unsigned y) {
            return __cut(data[x],data[y]);
        }

        T queryV(unsigned idx) {
            return data[idx]->val;
        }

        T queryS(unsigned idx) {
            return data[idx]->st;
        }

        void update(unsigned idx,T v) {
            splay(data[idx]);
            data[idx]->val=v;
        }

        T query(unsigned x,unsigned y) {
            split(x,y);
            return data[y]->st;
        }

        bool isLinked(unsigned x,unsigned y) {
            return !__isLinked(data[x],data[y]);
        }
    };
};
